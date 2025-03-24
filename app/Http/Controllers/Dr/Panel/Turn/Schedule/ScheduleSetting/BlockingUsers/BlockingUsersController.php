<?php

namespace App\Http\Controllers\Dr\Panel\Turn\Schedule\ScheduleSetting\BlockingUsers;

use App\Models\User;
use App\Models\Doctor;
use App\Models\SmsTemplate;
use Illuminate\Support\Str;
use App\Models\UserBlocking;
use Illuminate\Http\Request;
use App\Jobs\SendSmsNotificationJob;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Dr\Controller;
use Modules\SendOtp\App\Http\Services\MessageService;
use Modules\SendOtp\App\Http\Services\SMS\SmsService;

class BlockingUsersController extends Controller
{
    public function index(Request $request)
    {
        $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id;
        $clinicId = ($request->input('selectedClinicId') === 'default') ? null : $request->input('selectedClinicId');

        $blockedUsers = UserBlocking::with('user')
            ->where('doctor_id', $doctorId)
            ->where('clinic_id', $clinicId)
            ->get();

        $messages = SmsTemplate::with('user')->latest()->get();
        $users    = User::all();

        if ($request->ajax()) {
            return response()->json(['blockedUsers' => $blockedUsers]);
        }

        return view('dr.panel.turn.schedule.scheduleSetting.blocking_users.index', compact('blockedUsers', 'messages', 'users'));
    }

    public function store(Request $request)
    {
        $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id;

        try {
            $validated = $request->validate([
                'mobile'           => 'required|exists:users,mobile',
                'blocked_at'       => 'required|date',
                'unblocked_at'     => 'nullable|date|after:blocked_at',
                'reason'           => 'nullable|string|max:255',
                'selectedClinicId' => 'nullable|string',
            ]);

            $clinicId = $request->input('selectedClinicId') === 'default' ? null : $request->input('selectedClinicId');
            $user = User::where('mobile', $validated['mobile'])->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'کاربر یافت نشد!'], 422);
            }

            $isBlocked = UserBlocking::where('user_id', $user->id)
                ->where('doctor_id', $doctorId)
                ->where('clinic_id', $clinicId)
                ->where('status', 1)
                ->exists();

            if ($isBlocked) {
                return response()->json(['success' => false, 'message' => 'این کاربر قبلاً در این کلینیک مسدود شده است.'], 422);
            }

            $blockingUser = UserBlocking::create([
                'user_id'      => $user->id,
                'doctor_id'    => $doctorId,
                'clinic_id'    => $clinicId,
                'blocked_at'   => $validated['blocked_at'],
                'unblocked_at' => $validated['unblocked_at'] ?? null,
                'reason'       => $validated['reason'] ?? null,
                'status'       => 1,
            ]);

            // ارسال پیامک مسدودیت
            $doctor = Doctor::find($doctorId);
            $doctorName = $doctor->first_name . ' ' . $doctor->last_name;
            $message = "کاربر گرامی، شما توسط پزشک {$doctorName} در کلینیک انتخابی مسدود شده‌اید. جهت اطلاعات بیشتر تماس بگیرید.";

            SendSmsNotificationJob::dispatch(
                $message,
                [$user->mobile],
                100254, // شناسه قالب مسدودیت
                [$doctorName]
            )->delay(now()->addSeconds(5));

            return response()->json([
                'success'       => true,
                'message'       => 'کاربر با موفقیت مسدود شد و پیامک در صف قرار گرفت.',
                'blocking_user' => $blockingUser->load('user'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ذخیره‌سازی کاربر.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function storeMultiple(Request $request)
    {
        $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id;
        $clinicId = ($request->input('selectedClinicId') === 'default') ? null : $request->input('selectedClinicId');

        try {
            $validated = $request->validate([
                'mobiles'      => 'required|array',
                'mobiles.*'    => 'exists:users,mobile',
                'blocked_at'   => 'required|date',
                'unblocked_at' => 'nullable|date|after:blocked_at',
                'reason'       => 'nullable|string|max:255',
            ]);

            $blockedUsers = [];
            $alreadyBlocked = [];
            $recipients = [];

            foreach ($validated['mobiles'] as $mobile) {
                $user = User::where('mobile', $mobile)->first();
                if (!$user) {
                    continue;
                }

                $isBlocked = UserBlocking::where('user_id', $user->id)
                    ->where('doctor_id', $doctorId)
                    ->where('clinic_id', $clinicId)
                    ->where('status', 1)
                    ->exists();

                if ($isBlocked) {
                    $alreadyBlocked[] = $mobile;
                    continue;
                }

                $blockingUser = UserBlocking::create([
                    'user_id'      => $user->id,
                    'doctor_id'    => $doctorId,
                    'clinic_id'    => $clinicId,
                    'blocked_at'   => $validated['blocked_at'],
                    'unblocked_at' => $validated['unblocked_at'] ?? null,
                    'reason'       => $validated['reason'] ?? null,
                    'status'       => 1,
                ]);

                $blockedUsers[] = $blockingUser;
                $recipients[] = $mobile;
            }

            if (empty($blockedUsers) && !empty($alreadyBlocked)) {
                return response()->json(['success' => false, 'message' => 'کاربران انتخاب‌شده قبلاً مسدود شده‌اند.'], 422);
            }

            if (empty($blockedUsers)) {
                return response()->json(['success' => false, 'message' => 'هیچ کاربری برای مسدود کردن پیدا نشد.'], 422);
            }

            // ارسال پیامک مسدودیت برای همه کاربران جدید
            if (!empty($recipients)) {
                $doctor = Doctor::find($doctorId);
                $doctorName = $doctor->first_name . ' ' . $doctor->last_name;
                $message = "کاربر گرامی، شما توسط پزشک {$doctorName} در کلینیک انتخابی مسدود شده‌اید. جهت اطلاعات بیشتر تماس بگیرید.";

                SendSmsNotificationJob::dispatch(
                    $message,
                    $recipients,
                    100254, // شناسه قالب مسدودیت
                    [$doctorName]
                )->delay(now()->addSeconds(5));
            }

            return response()->json([
                'success'         => true,
                'message'         => 'کاربران با موفقیت مسدود شدند و پیامک در صف قرار گرفت.',
                'blocked_users'   => $blockedUsers,
                'already_blocked' => $alreadyBlocked,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ذخیره‌سازی کاربران.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $clinicId = ($request->input('selectedClinicId') === 'default') ? null : $request->input('selectedClinicId');

            $userBlocking = UserBlocking::where('id', $request->id)
                ->where('clinic_id', $clinicId)
                ->firstOrFail();

            $userBlocking->status = $request->status;
            $userBlocking->save();

            $user = $userBlocking->user;
            $doctor = $userBlocking->doctor;
            $doctorName = $doctor->first_name . ' ' . $doctor->last_name;

            // تعیین پیام و قالب بر اساس وضعیت
            if ($request->status == 1) {
                $message = "کاربر گرامی، شما توسط پزشک {$doctorName} در کلینیک انتخابی مسدود شده‌اید. جهت اطلاعات بیشتر تماس بگیرید.";
                $templateId = 100254; // قالب مسدودیت
            } else {
                $message = "کاربر گرامی، شما توسط پزشک {$doctorName} از حالت مسدودی خارج شدید. اکنون دسترسی شما فعال است.";
                $templateId = 100255; // قالب رفع مسدودیت
            }

            SendSmsNotificationJob::dispatch(
                $message,
                [$user->mobile],
                $templateId,
                [$doctorName]
            )->delay(now()->addSeconds(5));

            // ذخیره پیام در SmsTemplate
            SmsTemplate::create([
                'doctor_id'  => $doctor->id,
                'clinic_id'  => $clinicId,
                'user_id'    => $user->id,
                'identifier' => Str::random(11),
                'title'      => $request->status == 1 ? 'مسدودی کاربر' : 'رفع مسدودی',
                'content'    => $message,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'وضعیت با موفقیت به‌روزرسانی شد و پیامک در صف قرار گرفت.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در به‌روزرسانی وضعیت.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'content'            => 'required|string',
            'recipient_type'     => 'required|in:all,blocked,specific',
            'specific_recipient' => 'nullable|exists:users,mobile',
        ]);

        try {
            $recipients = [];
            if ($validated['recipient_type'] === 'all') {
                $recipients = User::pluck('mobile')->toArray();
            } elseif ($validated['recipient_type'] === 'blocked') {
                $recipients = UserBlocking::with('user')->get()->pluck('user.mobile')->toArray();
            } elseif ($validated['recipient_type'] === 'specific') {
                $user = User::where('mobile', $validated['specific_recipient'])->first();
                if (! $user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'شماره موبایل وارد شده در سیستم وجود ندارد.',
                    ], 422);
                }
                $recipients[] = $validated['specific_recipient'];
            }
            foreach ($recipients as $recipient) {
                /*  $messagesService = new MessageService(
      SmsService::create($validated['content'], $recipient)
     );
     $messagesService->send(); */
            }
            $user = User::where('mobile', $validated['specific_recipient'])->first();
            SmsTemplate::create([
                'doctor_id'  => Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id,
                'user_id'    => $user->id,
                'title'      => $validated['title'],
                'content'    => $validated['content'],
                'type'       => 'manual',
                'identifier' => uniqid(),
            ]);

            return response()->json(['success' => true, 'message' => 'پیام با موفقیت ارسال شد.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ارسال پیام!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getMessages()
    {
        $messages = SmsTemplate::with('user')->latest()->get();

        return response()->json($messages);
    }

 

    public function deleteMessage($id)
    {
        try {
            $message = SmsTemplate::findOrFail($id);
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'پیام با موفقیت حذف شد.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف پیام.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id, Request $request)
    {
        $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id;
        $clinicId = ($request->input('selectedClinicId') === 'default') ? null : $request->input('selectedClinicId');

        try {
            $userBlocking = UserBlocking::where('id', $id)
                ->where('doctor_id', $doctorId)
                ->where('clinic_id', $clinicId)
                ->firstOrFail();

            $userBlocking->delete();

            return response()->json([
                'success' => true,
                'message' => 'کاربر با موفقیت از لیست مسدودی حذف شد.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف کاربر.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
