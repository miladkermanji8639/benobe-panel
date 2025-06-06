<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorCounselingWorkSchedule extends Model
{
    use HasFactory;

    protected $table = 'doctor_counseling_work_schedules';

    protected $fillable = [
        'doctor_id',
        'clinic_id',
        'day',
        'is_working',
        'work_hours',
        'appointment_settings',
    ];

    protected $casts = [
        'is_working'           => 'boolean',
        'work_hours'           => 'array',
        'appointment_settings' => 'array',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
