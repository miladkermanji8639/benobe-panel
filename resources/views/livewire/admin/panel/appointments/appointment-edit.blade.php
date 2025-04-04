<div class="container-fluid py-4" dir="rtl">
  <div class="card shadow-lg border-0 rounded-3 overflow-hidden" style="background: #ffffff;">
    <div
      class="card-header bg-gradient-primary text-white p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          class="animate-bounce">
          <path d="M5 12h14M12 5l7 7-7 7" />
        </svg>
        <h5 class="mb-0 fw-bold text-shadow">ویرایش نوبت</h5>
      </div>
      <a href="{{ route('admin.panel.appointments.index') }}"
        class="btn btn-outline-light btn-sm rounded-pill px-4 d-flex align-items-center gap-2 hover:shadow-lg transition-all">
        <svg style="transform: rotate(180deg)" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2">
          <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        بازگشت
      </a>
    </div>

    <div class="card-body p-4">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
          <div class="row g-4">
            <div class="col-6 col-md-6 position-relative mt-5" wire:ignore>
              <select wire:model.live="doctor_id" class="form-select select2" id="doctor_id">
                <option value="">انتخاب کنید</option>
                @foreach ($doctors as $doctor)
                  <option value="{{ $doctor->id }}" {{ $doctor->id == $doctor_id ? 'selected' : '' }}>
                    {{ $doctor->full_name }}
                    ({{ $doctor->specialty->name ?? 'نامشخص' }})
                  </option>
                @endforeach
              </select>
              <label for="doctor_id" class="form-label">پزشک</label>
            </div>
            <div class="col-6 col-md-6 position-relative mt-5" wire:ignore>
              <select wire:model.live="patient_id" class="form-select select2" id="patient_id">
                <option value="">انتخاب کنید</option>
                @foreach ($patients as $patient)
                  <option value="{{ $patient->id }}" {{ $patient->id == $patient_id ? 'selected' : '' }}>
                    {{ $patient->full_name }}
                  </option>
                @endforeach
              </select>
              <label for="patient_id" class="form-label">بیمار (اختیاری)</label>
            </div>
            <div class="col-6 col-md-6 position-relative mt-5">
              <input type="text" wire:model="appointment_date" class="form-control jalali-datepicker text-end"
                id="appointment_date" data-jdp required>
              <label for="appointment_date" class="form-label">تاریخ نوبت</label>
            </div>
            <div class="col-6 col-md-6 position-relative mt-5" dir="rtl">
              <input type="text" wire:model="appointment_time" class="form-control flatpickr-time h-50"
                id="appointment_time" required>
              <label for="appointment_time" class="form-label">ساعت نوبت</label>
            </div>
            <div class="col-6 col-md-6 position-relative mt-5">
              <select wire:model="status" class="form-select" id="status">
                <option value="scheduled" {{ $status == 'scheduled' ? 'selected' : '' }}>برنامه‌ریزی‌شده</option>
                <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                <option value="attended" {{ $status == 'attended' ? 'selected' : '' }}>حضور یافته</option>
                <option value="missed" {{ $status == 'missed' ? 'selected' : '' }}>غایب</option>
                <option value="pending_review" {{ $status == 'pending_review' ? 'selected' : '' }}>در انتظار بررسی</option>
              </select>
              <label for="status" class="form-label">وضعیت نوبت</label>
            </div>
            <div class="col-6 col-md-6 position-relative mt-5">
              <select wire:model="payment_status" class="form-select" id="payment_status">
                <option value="paid" {{ $payment_status == 'paid' ? 'selected' : '' }}>پرداخت شده</option>
                <option value="unpaid" {{ $payment_status == 'unpaid' ? 'selected' : '' }}>پرداخت نشده</option>
                <option value="pending" {{ $payment_status == 'pending' ? 'selected' : '' }}>در انتظار پرداخت</option>
              </select>
              <label for="payment_status" class="form-label">وضعیت پرداخت</label>
            </div>
            <div class="col-6 col-md-6 position-relative mt-5">
              <input type="text" wire:model="tracking_code" class="form-control" id="tracking_code" placeholder=" ">
              <label for="tracking_code" class="form-label">کد رهگیری (اختیاری)</label>
            </div>
            <div class="col-6 col-md-6 position-relative mt-5">
              <input type="number" wire:model="fee" class="form-control" id="fee" placeholder=" ">
              <label for="fee" class="form-label">هزینه (تومان)</label>
            </div>
            <div class="col-12 position-relative mt-5">
              <textarea wire:model="notes" class="form-control" id="notes" rows="3" placeholder=" "></textarea>
              <label for="notes" class="form-label">یادداشت‌ها (اختیاری)</label>
            </div>
          </div>

          <div class="text-end mt-4 w-100 d-flex justify-content-end">
            <button wire:click="update"
              class="btn btn-primary px-5 py-2 d-flex align-items-center gap-2 shadow-lg hover:shadow-xl transition-all">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                <path d="M17 21v-8H7v8M7 3v5h8" />
              </svg>
              ذخیره تغییرات
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .bg-gradient-primary {
      background: linear-gradient(90deg, #6b7280, #374151);
    }

    .card {
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .form-control,
    .form-select {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 12px 15px;
      font-size: 14px;
      transition: all 0.3s ease;
      height: 48px;
      background: #fafafa;
      width: 100%;
      text-align: right;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #6b7280;
      box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.2);
      background: #fff;
    }

    .form-label {
      position: absolute;
      top: -25px;
      right: 15px;
      color: #374151;
      font-size: 12px;
      background: #ffffff;
      padding: 0 5px;
      pointer-events: none;
    }

    .btn-primary {
      background: linear-gradient(90deg, #6b7280, #374151);
      border: none;
      color: white;
      font-weight: 600;
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #4b5563, #1f2937);
      transform: translateY(-2px);
    }

    .btn-outline-light {
      border-color: rgba(255, 255, 255, 0.8);
    }

    .btn-outline-light:hover {
      background: rgba(255, 255, 255, 0.15);
      transform: translateY(-2px);
    }

    .animate-bounce {
      animation: bounce 1s infinite;
    }

    @keyframes bounce {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-5px);
      }
    }

    .text-shadow {
      text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .select2-container {
      width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
      height: 48px;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      background: #fafafa;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 46px;
      padding-right: 15px;
      text-align: right;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 46px;
    }
  </style>
  <script>
    document.addEventListener('livewire:init', function() {
      // مقداردهی اولیه flatpickr برای انتخاب زمان
      flatpickr('#appointment_time', {
        enableTime: true, // فعال کردن انتخاب زمان
        noCalendar: true, // غیرفعال کردن تقویم (فقط زمان)
        dateFormat: 'H:i', // فرمت 24 ساعته (مثل 14:30)
        time_24hr: true, // استفاده از فرمت 24 ساعته
        minuteIncrement: 5, // افزایش دقیقه‌ها با گام 5
        defaultDate: "{{ $appointment_time ?? '' }}", // مقدار پیش‌فرض از دیتابیس
        locale: { // پشتیبانی از RTL و فارسی
          firstDayOfWeek: 6, // شروع هفته از شنبه
          weekdays: {
            shorthand: ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'],
            longhand: ['شنبه', 'یک‌شنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه'],
          },
          meridiem: {
            am: 'ق.ظ',
            pm: 'ب.ظ'
          } // در صورت نیاز به 12 ساعته
        },
        onChange: function(selectedDates, dateStr) {
          // ارسال مقدار به Livewire
          if (dateStr) {
            @this.set('appointment_time', dateStr);
          }
        }
      });
      
      Livewire.on('show-alert', (event) => {
        toastr[event.type](event.message);
      });
      
      // Select2
      $('#doctor_id').select2({
        dir: 'rtl',
        placeholder: 'انتخاب کنید',
        width: '100%'
      });
      $('#patient_id').select2({
        dir: 'rtl',
        placeholder: 'انتخاب کنید',
        width: '100%'
      });

      $('#doctor_id').on('change', function() {
        @this.set('doctor_id', $(this).val());
      });
      $('#patient_id').on('change', function() {
        @this.set('patient_id', $(this).val());
      });

      // Jalali Datepicker
      jalaliDatepicker.startWatch({
        minDate: "attr",
        maxDate: "attr",
        showTodayBtn: true,
        showEmptyBtn: true,
        time: false,
        dateFormatter: function(unix) {
          return new Date(unix).toLocaleDateString('fa-IR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
          });
        }
      });
      document.getElementById('appointment_date').addEventListener('change', function() {
        @this.set('appointment_date', this.value);
      });
    });
  </script>
</div>