<?php
namespace App\Http\Controllers\Dr\Panel\Profile;

use App\Http\Controllers\Dr\Controller;
use App\Http\Requests\DoctorSpecialtyRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\AcademicDegree;
use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use App\Models\Otp;
use App\Models\SubSpecialty;
use App\Traits\HandlesRateLimiting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\SendOtp\App\Http\Services\MessageService;
use Modules\SendOtp\App\Http\Services\SMS\SmsService;

class DrProfileController extends Controller
{
    use HandlesRateLimiting;

    protected function getAuthenticatedDoctor(): Doctor
    {
        $doctor = Auth::guard('doctor')->user();
        if (! $doctor instanceof Doctor) {
            throw new \Exception('کاربر احراز هویت شده از نوع Doctor نیست یا وجود ندارد.');
        }
        return $doctor;
    }

    public function uploadPhoto(Request $request)
    {
        if (! $request->hasFile('photo')) {
            return response()->json(['success' => false, 'message' => 'لطفاً یک عکس انتخاب کنید!'], 400);
        }

        $request->validate([
            'photo' => 'image',
        ]);

        try {
            $doctor = $this->getAuthenticatedDoctor();
            $path   = $request->file('photo')->store('profile-photos', 'public');
            $doctor->update(['profile_photo_path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'عکس پروفایل با موفقیت آپدیت شد.',
                'path'    => Storage::url($path),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در آپلود عکس: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit()
    {
        $doctor                   = $this->getAuthenticatedDoctor();
        $currentSpecialty         = DoctorSpecialty::where('doctor_id', $doctor->id)->first();
        $specialtyName            = $currentSpecialty->specialty_title ?? 'نامشخص';
        $specialties              = DoctorSpecialty::where('doctor_id', $doctor->id)->get();
        $doctorSpecialties        = DoctorSpecialty::where('doctor_id', $doctor->id)->get();
        $existingSpecialtiesCount = DoctorSpecialty::where('doctor_id', $doctor->id)->count();
        $doctorSpecialtyId        = DoctorSpecialty::where('doctor_id', $doctor->id)->first();
        $academic_degrees         = AcademicDegree::active()
            ->orderBy('sort_order')
            ->get();
        $messengers         = $doctor->messengers;
        $sub_specialties    = SubSpecialty::getOptimizedList();
        $incompleteSections = $doctor->getIncompleteProfileSections();

        return view("dr.panel.profile.edit-profile", compact([
            'specialtyName',
            'academic_degrees',
            'sub_specialties',
            'currentSpecialty',
            'specialties',
            'doctorSpecialtyId',
            'existingSpecialtiesCount',
            'messengers',
            'doctor',
            'incompleteSections',
        ]));
    }

    public function DrSpecialtyUpdate(DoctorSpecialtyRequest $request)
    {
        $key      = 'update_static_password_' . $request->ip();
        $response = $this->checkRateLimit($key);
        if ($response) {
            return $response;
        }

        $doctor    = $this->getAuthenticatedDoctor();
        $validator = Validator::make($request->all(), $request->rules());
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            $mainSpecialty = DoctorSpecialty::updateOrCreate(
                ['doctor_id' => $doctor->id, 'is_main' => true],
                [
                    'academic_degree_id' => $request->academic_degree_id,
                    'specialty_id'       => $request->specialty_id,
                    'specialty_title'    => $request->specialty_title,
                ]
            );

            if ($request->has('degrees') && $request->has('specialties')) {
                $additionalSpecialtiesCount = 0;
                foreach ($request->degrees as $index => $degreeId) {
                    if (! empty($degreeId) && ! empty($request->specialties[$index])) {
                        $duplicateSpecialty = DoctorSpecialty::where('doctor_id', $doctor->id)
                            ->where('specialty_id', $request->specialties[$index])
                            ->exists();
                        if ($duplicateSpecialty) {
                            continue;
                        }
                        $additionalSpecialtiesCount++;
                        if ($additionalSpecialtiesCount > 2) {
                            break;
                        }
                        DoctorSpecialty::create([
                            'doctor_id'          => $doctor->id,
                            'academic_degree_id' => $degreeId,
                            'specialty_id'       => $request->specialties[$index],
                            'specialty_title'    => $request->titles[$index] ?? null,
                            'is_main'            => false,
                        ]);
                    }
                }
            }

            DB::commit();
            $this->updateProfileCompletion($doctor);
            $updatedSpecialties = DoctorSpecialty::where('doctor_id', $doctor->id)->where('is_main', 0)->get();

            return response()->json([
                'success'     => true,
                'message'     => 'اطلاعات تخصص با موفقیت به‌روزرسانی شد.',
                'specialties' => $updatedSpecialties,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'خطایی در به‌روزرسانی اطلاعات تخصص رخ داد.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function DrUUIDUpdate(Request $request)
    {
        $key      = 'DrSpecialtyUpdate_' . $request->ip();
        $response = $this->checkRateLimit($key);
        if ($response) {
            return $response;
        }

        $doctor    = $this->getAuthenticatedDoctor();
        $validator = Validator::make($request->all(), [
            'uuid' => [
                'string',
                'nullable',
                'unique:doctors,uuid,' . $doctor->id,
                'regex:/^[a-zA-Z0-9_-]+$/',
                function ($attribute, $value, $fail) use ($doctor) {
                    $existingDoctor = Doctor::where('uuid', $value)
                        ->where('id', '!=', $doctor->id)
                        ->first();
                    if ($existingDoctor) {
                        $fail('این UUID قبلاً توسط پزشک دیگری ثبت شده است');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $doctor->uuid = $request->uuid;
        if ($doctor->save()) {
            $this->updateProfileCompletion($doctor);
            return response()->json([
                'success' => true,
                'message' => 'آیدی شما با موفقیت تغییر کرد',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'خطایی رخ داده',
            ]);
        }
    }

    public function deleteSpecialty($id)
    {
        try {
            $specialty = DoctorSpecialty::findOrFail($id);
            $specialty->delete();
            return response()->json([
                'success' => true,
                'message' => 'تخصص با موفقیت حذف شد.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف تخصص.',
            ], 500);
        }
    }

    public function updateMessengers(Request $request)
    {
        $doctor   = $this->getAuthenticatedDoctor();
        $key      = "updateMessengers_" . $request->ip();
        $response = $this->checkRateLimit($key);
        if ($response) {
            return $response;
        }

        $request->validate([
            'ita_phone'      => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(?!09{1}(\\d)\\1{8}$)09(?:01|02|03|12|13|14|15|16|18|19|20|21|22|30|33|35|36|38|39|90|91|92|93|94)\\d{7}$/i',
            ],
            'ita_username'   => 'nullable|string|max:100',
            'whatsapp_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(?!09{1}(\\d)\\1{8}$)09(?:01|02|03|12|13|14|15|16|18|19|20|21|22|30|33|35|36|38|39|90|91|92|93|94)\\d{7}$/i',
            ],
            'secure_call'    => 'nullable|boolean',
        ], [
            'ita_phone.regex'      => 'شماره موبایل ایتا را به درستی وارد کنید.',
            'whatsapp_phone.regex' => 'شماره موبایل واتس‌اپ را به درستی وارد کنید.',
        ]);

        $doctor->messengers()->updateOrCreate(
            ['messenger_type' => 'ita'],
            [
                'phone_number'   => $request->ita_phone,
                'username'       => $request->ita_username,
                'is_secure_call' => $request->secure_call,
            ]
        );

        $doctor->messengers()->updateOrCreate(
            ['messenger_type' => 'whatsapp'],
            [
                'phone_number'   => $request->whatsapp_phone,
                'is_secure_call' => $request->secure_call,
            ]
        );

        $this->updateProfileCompletion($doctor);

        return response()->json([
            'success' => true,
            'message' => 'اطلاعات پیام‌رسان‌ها با موفقیت به‌روزرسانی شد.',
        ]);
    }

    public function updateStaticPassword(Request $request)
    {
        $key      = 'update_static_password_' . $request->ip();
        $response = $this->checkRateLimit($key);
        if ($response) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'static_password_enabled' => 'required|boolean',
            'password'                => 'required_if:static_password_enabled,true|string|min:6|confirmed',
        ], [
            'password.required_if' => 'رمز عبور الزامی است.',
            'password.confirmed'   => 'تکرار رمز عبور با رمز عبور اصلی مطابقت ندارد.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $doctor                          = $this->getAuthenticatedDoctor();
            $doctor->static_password_enabled = $request->static_password_enabled;
            if ($request->static_password_enabled) {
                $doctor->password = Hash::make($request->password);
            } else {
                $doctor->password = null;
            }
            $doctor->save();

            return response()->json([
                'success' => true,
                'message' => 'تنظیمات رمز عبور ثابت با موفقیت به‌روزرسانی شد.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطای سرور در به‌روزرسانی رمز عبور ثابت',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateTwoFactorAuth(Request $request)
    {
        $key      = 'update_two_factor_auth_' . $request->ip();
        $response = $this->checkRateLimit($key);
        if ($response) {
            return $response;
        }

        $request->validate([
            'two_factor_enabled' => 'required|boolean',
            'two_factor_secret'  => $request->two_factor_enabled ? 'required|string|min:6' : 'nullable',
        ]);

        $doctor                            = $this->getAuthenticatedDoctor();
        $doctor->two_factor_secret_enabled = $request->two_factor_enabled;
        $doctor->two_factor_secret         = $request->two_factor_enabled ? Hash::make($request->two_factor_secret) : null;
        $doctor->save();

        return response()->json([
            'success' => true,
            'message' => 'تنظیمات گذرواژه دو مرحله‌ای با موفقیت به‌روزرسانی شد.',
        ]);
    }

    public function niceId()
    {
        return view("dr.panel.profile.edit-niceId");
    }

    public function update_profile(UpdateProfileRequest $request)
    {
        $key      = 'update_profile_' . $request->ip();
        $response = $this->checkRateLimit($key);
        if ($response) {
            return $response;
        }

        try {
            $doctor = $this->getAuthenticatedDoctor();
            $doctor->update([
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'national_code'  => $request->national_code,
                'license_number' => $request->license_number,
                'bio'            => $request->description,
            ]);

            $this->updateProfileCompletion($doctor);

            return response()->json([
                'success' => true,
                'message' => 'پروفایل با موفقیت به‌روزرسانی شد.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطای سرور در به‌روزرسانی پروفایل',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function checkProfileCompleteness()
    {
        try {
            $doctor             = $this->getAuthenticatedDoctor();
            $incompleteSections = $doctor->getIncompleteProfileSections();
            return response()->json([
                'success'             => true,
                'profile_completed'   => $doctor->profile_completed,
                'incomplete_sections' => $incompleteSections,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در بررسی وضعیت پروفایل',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function updateProfileCompletion(Doctor $doctor)
    {
        $doctor->profile_completed = $doctor->isProfileComplete();
        $doctor->save();
    }

    public function sendMobileOtp(Request $request)
    {
        $key      = 'sendMobileOtp_' . $request->ip();
        $response = $this->checkRateLimit($key, '3', '1');
        if ($response) {
            return $response;
        }

        $request->validate([
            'mobile' => [
                'required',
                'regex:/^(?!09{1}(\d)\1{8}$)09(?:01|02|03|12|13|14|15|16|18|19|20|21|22|30|33|35|36|38|39|90|91|92|93|94)\d{7}$/',
                function ($attribute, $value, $fail) {
                    $existingDoctor = Doctor::where('mobile', $value)->first();
                    if ($existingDoctor) {
                        $fail('این شماره موبایل قبلاً ثبت شده است');
                    }
                },
            ],
        ], [
            'mobile.required' => 'شماره موبایل الزامی است',
            'mobile.regex'    => 'شماره موبایل نامعتبر است',
        ]);

        $doctor = $this->getAuthenticatedDoctor();
        return $this->sendOtp($doctor, $request->mobile);
    }

    private function sendOtp(Doctor $doctor, $newMobile)
    {
        $otpCode = rand(1000, 9999);
        $token   = Str::random(60);
        Otp::create([
            'token'     => $token,
            'doctor_id' => $doctor->id,
            'otp_code'  => $otpCode,
            'login_id'  => $newMobile,
            'type'      => 0,
        ]);

        $messagesService = new MessageService(
            SmsService::create(100253, $newMobile, [$otpCode])
        );
        $messagesService->send();

        return response()->json(['token' => $token, 'otp_code' => $otpCode]);
    }

    public function mobileConfirm(Request $request, $token)
    {
        $key      = 'mobileConfirm' . $request->ip();
        $response = $this->checkRateLimit($key);
        if ($response) {
            return $response;
        }

        $validator = Validator::make($request->all(), [
            'otp'    => 'required|array',
            'otp.*'  => 'required|numeric|digits:1',
            'mobile' => [
                'required',
                'regex:/^(?!09{1}(\d)\1{8}$)09(?:01|02|03|12|13|14|15|16|18|19|20|21|22|30|33|35|36|38|39|90|91|92|93|94)\d{7}$/',
                function ($attribute, $value, $fail) {
                    $existingDoctor = Doctor::where('mobile', $value)
                        ->where('id', '!=', Auth::guard('doctor')->id())
                        ->first();
                    if ($existingDoctor) {
                        $fail('این شماره موبایل قبلاً توسط پزشک دیگری ثبت شده است');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $otp = Otp::where('token', $token)
            ->where('used', 0)
            ->where('created_at', '>=', Carbon::now()->subMinutes(2))
            ->first();

        if (! $otp) {
            return response()->json([
                'success' => false,
                'message' => 'کد تایید منقضی شده است',
            ], 422);
        }

        $otpCode = implode('', $request->otp);
        if ($otp->otp_code !== $otpCode) {
            return response()->json([
                'success' => false,
                'message' => 'کد تایید صحیح نمی‌باشد',
            ], 422);
        }

        $currentDoctor = $this->getAuthenticatedDoctor();
        $updateResult  = $currentDoctor->update([
            'mobile' => $request->mobile,
        ]);

        if (! $updateResult) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در به‌روزرسانی شماره موبایل',
            ], 500);
        }

        $otp->update(['used' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'شماره موبایل با موفقیت تغییر یافت',
            'mobile'  => $request->mobile,
        ]);
    }

    public function destroy(string $id)
    {
        //
    }
}
