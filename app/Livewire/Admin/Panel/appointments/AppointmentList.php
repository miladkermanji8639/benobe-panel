<?php


namespace App\Livewire\Admin\Panel\Appointments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appointment;

class AppointmentList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['deleteAppointmentConfirmed' => 'deleteAppointment'];

    public $perPage = 10; // پیجینیشن اصلی صفحه
    public $appointmentsPerPage = 5; // پیجینیشن محلی برای نوبت‌های هر پزشک
    public $search = '';
    public $readyToLoad = false;
    public $expandedDoctors = [];
    public $doctorPages = []; // آرایه برای ذخیره صفحه فعلی نوبت‌ها برای هر پزشک

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->perPage = max($this->perPage, 1);
        $this->appointmentsPerPage = max($this->appointmentsPerPage, 1);
    }

    public function loadAppointments()
    {
        $this->readyToLoad = true;
    }

    public function toggleDoctor($doctorId)
    {
        if (in_array($doctorId, $this->expandedDoctors)) {
            $this->expandedDoctors = array_diff($this->expandedDoctors, [$doctorId]);
        } else {
            $this->expandedDoctors[] = $doctorId;
            // مقدار پیش‌فرض صفحه برای پزشک جدید
            if (!isset($this->doctorPages[$doctorId])) {
                $this->doctorPages[$doctorId] = 1;
            }
        }
    }

    public function setDoctorPage($doctorId, $page)
    {
        $this->doctorPages[$doctorId] = max(1, $page); // مطمئن می‌شیم صفحه کمتر از 1 نشه
    }

    public function toggleStatus($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => $appointment->status === 'scheduled' ? 'cancelled' : 'scheduled']);
        $this->dispatch('show-alert', type: $appointment->status === 'scheduled' ? 'success' : 'info', message: $appointment->status === 'scheduled' ? 'نوبت فعال شد!' : 'نوبت لغو شد!');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    public function deleteAppointment($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        $this->dispatch('show-alert', type: 'success', message: 'نوبت حذف شد!');
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->doctorPages = []; // ریست کردن پیجینیشن محلی هنگام جستجو
    }

    private function getAppointmentsQuery()
    {
        return Appointment::with('doctor', 'patient')
            ->where(function ($query) {
                $query->whereHas('doctor', function ($q) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $this->search . '%'])
                      ->orWhere('national_code', 'like', '%' . $this->search . '%');
                })->orWhereHas('patient', function ($q) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $this->search . '%'])
                      ->orWhere('national_code', 'like', '%' . $this->search . '%');
                })->orWhere('tracking_code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('appointment_date', 'desc');
    }

    public function render()
    {
        $doctors = $this->readyToLoad ? $this->getAppointmentsQuery()
            ->get()
            ->groupBy('doctor_id')
            ->map(function ($appointments, $doctorId) {
                $currentPage = $this->doctorPages[$doctorId] ?? 1;
                $paginatedAppointments = $appointments->forPage($currentPage, $this->appointmentsPerPage);
                return [
                    'doctor' => $appointments->first()->doctor,
                    'appointments' => $paginatedAppointments->values(), // تبدیل به آرایه معمولی
                    'totalAppointments' => $appointments->count(),
                    'currentPage' => $currentPage,
                    'lastPage' => ceil($appointments->count() / $this->appointmentsPerPage),
                ];
            }) : [];

        return view('livewire.admin.panel.appointments.appointment-list', [
            'doctors' => $doctors,
        ]);
    }
}
