<?php

namespace App\Livewire\Admin\Doctors;

use App\Models\Doctors\OrderVisit;
use Livewire\Component;
use Livewire\WithPagination;

class OrderVisitList extends Component
{
    use WithPagination;

    public $search  = '';
    public $perPage = 100; // صفحه‌بندی 10 تایی

    protected $paginationTheme = 'bootstrap'; // استفاده از تم بوت‌استرپ برای صفحه‌بندی

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orderVisits = OrderVisit::with(['user', 'doctor', 'clinic'])
            ->where(function ($query) {
                $query->whereHas('user', fn ($q) => $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $this->search . '%']))
                    ->orWhere('mobile', 'like', '%' . $this->search . '%')
                    ->orWhere('tracking_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('clinic', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            })
            ->orderBy('payment_date', 'desc')
            ->paginate($this->perPage);

        $totalAmount = OrderVisit::where(function ($query) {
            $query->whereHas('user', fn ($q) => $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $this->search . '%']))
                ->orWhere('mobile', 'like', '%' . $this->search . '%')
                ->orWhere('tracking_code', 'like', '%' . $this->search . '%')
                ->orWhereHas('clinic', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));
        })->sum('amount');

        return view('livewire.admin.doctors.order-visit-list', [
            'orderVisits' => $orderVisits,
            'totalAmount' => $totalAmount,
        ]);
    }
}
