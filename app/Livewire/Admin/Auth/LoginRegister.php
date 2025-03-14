<?php

namespace App\Livewire\Admin\Auth;

use App\Models\Otp;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\LoginSession;
use App\Models\Admin\Manager;
use Illuminate\Support\Facades\Auth;
use Modules\SendOtp\App\Http\Services\MessageService;
use Modules\SendOtp\App\Http\Services\SMS\SmsService;
use App\Http\Services\LoginAttemptsService\LoginAttemptsService;

class LoginRegister extends Component
{
    public $mobile;

    public function mount()
    {
        if (Auth::guard('manager')->check()) {
            $this->redirect(route('admin-panel'));
        } elseif (session('current_step') === 2) {
            $this->redirect(route('admin.auth.login-confirm-form', ['token' => session('otp_token')]), navigate: true);
        } elseif (session('current_step') === 3) {
            $this->redirect(route('admin.auth.login-user-pass-form'), navigate: true);
        }
        session(['current_step' => 1]);
    }

    // تابع جدید برای فرمت کردن زمان
    private function formatTime($seconds)
    {
        if (is_null($seconds) || $seconds < 0) {
            return '0 دقیقه و 0 ثانیه';
        }
        $minutes = floor($seconds / 60);
        $remainingSeconds = round($seconds % 60);
        return "$minutes دقیقه و $remainingSeconds ثانیه";
    }

    public function loginRegister()
    {
        $this->validate([
            'mobile' => [
                'required',
                'string',
                'regex:/^(?!09{1}(\d)\1{8}$)09(?:01|02|03|12|13|14|15|16|18|19|20|21|22|30|33|35|36|38|39|90|91|92|93|94)\d{7}$/'
            ],
        ], [
            'mobile.required' => 'لطفاً شماره موبایل را وارد کنید.',
            'mobile.regex' => 'شماره موبایل باید فرمت معتبر داشته باشد (مثلاً 09181234567).',
        ]);

        $mobile = preg_replace('/^(\+98|98|0)/', '', $this->mobile);
        $formattedMobile = '0' . $mobile;

        $manager = Manager::where('mobile', $formattedMobile)->first();
        $loginAttempts = new LoginAttemptsService();

        if (!$manager) {
            $loginAttempts->incrementLoginAttempt(null, $formattedMobile, null, null, null);
            $this->addError('mobile', 'کاربری با این شماره تلفن وجود ندارد.');
            return;
        }

        if ($manager->status !== 1) {
            $loginAttempts->incrementLoginAttempt($manager->id, $formattedMobile, '', '', $manager->id);
            $this->addError('mobile', 'حساب کاربری شما فعال نیست.');
            return;
        }

        if ($loginAttempts->isLocked($formattedMobile)) {
            $remainingTime = $loginAttempts->getRemainingLockTime($formattedMobile);
            $formattedTime = $this->formatTime($remainingTime);
            $this->addError('mobile', "شما بیش از حد تلاش کرده‌اید. لطفاً $formattedTime صبر کنید.");
            $this->dispatch('rateLimitExceeded', remainingTime: $remainingTime);
            return;
        }

        $loginAttempts->incrementLoginAttempt($manager->id, $formattedMobile, '', '', $manager->id);
        session(['step1_completed' => true]);

        $otpCode = rand(1000, 9999);
        $token = Str::random(60);

        Otp::create([
            'token' => $token,
            'manager_id' => $manager->id,
            'otp_code' => $otpCode,
            'login_id' => $manager->mobile,
            'type' => 0,
        ]);

        // اضافه کردن ردیف به LoginSession
        LoginSession::create([
            'token' => $token,
            'manager_id' => $manager->id,
            'step' => 2, // استپ 2 برای تأیید کد OTP
            'expires_at' => now()->addMinutes(10),
        ]);

        $messagesService = new MessageService(
            SmsService::create(100253, $manager->mobile, [$otpCode])
        );
        $messagesService->send();

        session(['current_step' => 2, 'otp_token' => $token]);
        $this->dispatch('otpSent', token: $token);
        $this->redirect(route('admin.auth.login-confirm-form', ['token' => $token]), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.auth.login-register')
            ->layout('admin.layouts.admin-auth');
    }
}