<?php
namespace App\Models;

use App\Models\User;
use App\Models\Specialty;
use Morilog\Jalali\Jalalian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'pattern_id',
        'insurance_id',
        'appointment_type',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'description',

        'tracking_code',
        'fee',
        'appointment_category',
        'location',
        'notification_sent',
        'include_holidays',
        'disabled_days',
    ];
    protected $casts = [
        'appointment_date' => 'date',           // تبدیل به Carbon برای تاریخ
        'appointment_time' => 'datetime:H:i:s', // تبدیل به Carbon برای زمان
    ];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }
    public function pattern()
    {
        return $this->belongsTo(AppointmentPattern::class);
    }
    public function getJalaliAppointmentDateAttribute()
    {
        return Jalalian::fromCarbon($this->appointment_date)->format('Y/m/d');
    }

    // Accessor برای وضعیت پرداخت (اگر وجود داشته باشه)
    public function getPaymentStatusLabelAttribute()
    {
        switch ($this->payment_status) {
            case 'paid':
                return 'پرداخت شده';
            case 'unpaid':
                return 'پرداخت نشده';
            case 'pending':
                return 'در انتظار پرداخت';
            default:
                return 'نامشخص';
        }
    }
}
