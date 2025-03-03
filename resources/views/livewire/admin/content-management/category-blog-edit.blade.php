<div class="container-fluid py-4">
    <!-- هدر اصلی -->
    <div class="bg-light text-dark p-4 rounded-top border">
        <div class="d-flex align-items-center">
            <i class="fas fa-edit me-3"></i>
            <h5 class="mb-0 fw-bold">ویرایش دسته‌بندی</h5>
        </div>
    </div>

    <!-- بدنه اصلی -->
    <div class="bg-white p-4 rounded-bottom shadow-sm">
        <form wire:submit.prevent="update">
            <div class="row g-4">
                <!-- نام دسته‌بندی -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">نام دسته‌بندی</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger d-block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- دکمه‌ها -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.content-management.category-blog.index') }}" class="btn btn-outline-warning">
                    <i class="fas fa-arrow-right me-2"></i> بازگشت
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i> ویرایش
                </button>
            </div>
        </form>
    </div>

    <!-- استایل‌ها -->
    <style>
        .bg-light {
            background-color: #f8f9fa !important;
        }

        .border {
            border-color: #dee2e6 !important;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            border-radius: 0.375rem;
            padding: 0.75rem 1.5rem;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-outline-warning {
            color: #ffc107;
            border-color: #ffc107;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            color: #fff;
        }
    </style>

    <!-- اسکریپت SweetAlert و Toastr -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toast', (message, options = {}) => {
                if (typeof toastr === 'undefined') {
                    console.error('Toastr is not loaded!');
                    return;
                }
                const type = options.type || 'info';
                if (type === 'success') {
                    toastr.success(message, '', {
                        positionClass: options.position || 'toast-top-right',
                        timeOut: options.timeOut || 3000,
                        progressBar: options.progressBar || false,
                    });
                } else if (type === 'error') {
                    toastr.error(message, '', {
                        positionClass: options.position || 'toast-top-right',
                        timeOut: options.timeOut || 3000,
                        progressBar: options.progressBar || false,
                    });
                } else {
                    toastr.info(message, '', {
                        positionClass: options.position || 'toast-top-right',
                        timeOut: options.timeOut || 3000,
                        progressBar: options.progressBar || false,
                    });
                }
            });
        });
    </script>
</div>