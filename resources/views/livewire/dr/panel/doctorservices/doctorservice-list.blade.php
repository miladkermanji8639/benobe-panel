<div class="container-fluid py-2" dir="rtl" wire:init="loadDoctorServices">
    <div class="glass-header text-white p-3 rounded-3 mb-5 shadow-lg d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h1 class="m-0 h3 font-thin flex-grow-1" style="min-width: 200px;">مدیریت خدمات</h1>
        <div class="input-group flex-grow-1 position-relative" style="max-width: 400px;">
            <input type="text" class="form-control border-0 shadow-none bg-white text-dark ps-5 rounded-3" wire:model.live="search" placeholder="جستجو در خدمات..." style="padding-right: 23px">
            <span class="search-icon position-absolute top-50 start-0 translate-middle-y ms-3" style="z-index: 5; top: 11px; right: 5px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                    <path d="M11 3a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12zm5-1l5 5" />
                </svg>
            </span>
        </div>
        <div class="d-flex gap-2 flex-shrink-0 flex-wrap justify-content-center mt-md-2 buttons-container">
            <a href="{{ route('dr.panel.doctor-services.create') }}" class="btn btn-gradient-success rounded-pill px-4 d-flex align-items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                <span>افزودن خدمت</span>
            </a>
            <button wire:click="deleteSelected" class="btn btn-gradient-danger rounded-pill px-4 d-flex align-items-center gap-2" @if (empty($selectedDoctorServices)) disabled @endif>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                </svg>
                <span>حذف انتخاب‌شده‌ها</span>
            </button>
        </div>
    </div>

    <div class="container-fluid px-0">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-hover w-100 m-0">
                        <thead class="glass-header text-white">
                            <tr>
                                <th class="text-center align-middle" style="width: 50px;">
                                    <input type="checkbox" wire:model.live="selectAll" class="form-check-input m-0">
                                </th>
                                <th class="text-center align-middle" style="width: 70px;">ردیف</th>
                                <th class="align-middle">نام خدمت</th>
                                <th class="align-middle">توضیحات</th>
                                <th class="align-middle">زمان</th>
                                <th class="align-middle">قیمت</th>
                                <th class="align-middle">تخفیف</th>
                                <th class="align-middle">قیمت نهایی</th>
                                <th class="text-center align-middle" style="width: 100px;">وضعیت</th>
                                <th class="text-center align-middle" style="width: 150px;">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($readyToLoad)
                                @forelse ($doctorServices as $index => $item)
                                    @include('livewire.dr.panel.doctorservices.doctor-service-tree', ['service' => $item, 'level' => 0, 'index' => $doctorServices->firstItem() + $index])
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-5">
                                            <div class="d-flex justify-content-center align-items-center flex-column">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-muted mb-3">
                                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                                </svg>
                                                <p class="text-muted fw-medium">هیچ سرویسی یافت نشد.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="12" class="text-center py-5">در حال بارگذاری خدمات...</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4 px-4 flex-wrap gap-3">
                    <div class="text-muted">نمایش {{ $doctorServices ? $doctorServices->firstItem() : 0 }} تا {{ $doctorServices ? $doctorServices->lastItem() : 0 }} از {{ $doctorServices ? $doctorServices->total() : 0 }} ردیف</div>
                    @if ($doctorServices && $doctorServices->hasPages())
                        {{ $doctorServices->links('livewire::bootstrap') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on('show-alert', (event) => {
                toastr[event.type](event.message);
            });

            Livewire.on('confirm-delete', (event) => {
                Swal.fire({
                    title: 'حذف خدمت',
                    text: 'آیا مطمئن هستید که می‌خواهید این خدمت را حذف کنید؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'بله، حذف کن',
                    cancelButtonText: 'خیر'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteDoctorServiceConfirmed', { id: event.id });
                    }
                });
            });
        });
    </script>
    
</div>