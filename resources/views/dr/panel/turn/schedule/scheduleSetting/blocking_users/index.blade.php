@extends('dr.panel.layouts.master')
@section('styles')
  <link type="text/css" href="{{ asset('dr-assets/panel/css/panel.css') }}" rel="stylesheet" />
  <link type="text/css"
    href="{{ asset('dr-assets/panel/turn/schedule/schedule-setting/blocking-users/blocking-user.css') }}"
    rel="stylesheet" />

@endsection
@section('site-header')
  {{ 'به نوبه | پنل دکتر' }}
@endsection
@section('content')
@section('bread-crumb-title', 'مدیریت کاربران مسدود')
@include('dr.panel.my-tools.loader-btn')
<div class="blocking_users_content">
  <div class="container-fluid">
    <!-- جدول کاربران مسدود -->
    <div class="">
      <div class="card-header">
        <h5 class="mb-0 fw-bold">مدیریت کاربران مسدود</h5>
      </div>
      <div class="card-body">
        <!-- فیلد جستجو -->
        <div class="d-flex justify-content-between p-2 mb-3">
          <div class="search-container w-100">
            <input type="text" id="searchInput" class="search-input" placeholder="جستجو (نام، موبایل، دلیل)...">
            <span class="search-icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                <path d="M11 3a8 8 0 100 16 8 8 0 000-16zm0 14a6 6 0 110-12 6 6 0 010 12zm5-1l5 5" />
              </svg>
            </span>
          </div>
          <div class="btn-container">
            <button class="btn my-btn-primary h-50" data-bs-toggle="modal" data-bs-target="#addUserModal">افزودن
              کاربر</button>
          </div>
        </div>
        <div class="table-responsive">
          <table id="blockedUsersTable" class="table text-center">
            <thead>
              <tr>
                <th>نام کاربر</th>
                <th>شماره موبایل</th>
                <th>تاریخ شروع</th>
                <th>تاریخ پایان</th>
                <th>دلیل</th>
                <th>وضعیت</th>
                <th>عملیات</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($blockedUsers as $blockedUser)
                <tr data-id="{{ $blockedUser->id }}">
                  <td>{{ $blockedUser->user->first_name }} {{ $blockedUser->user->last_name }}</td>
                  <td>{{ $blockedUser->user->mobile }}</td>
                  <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($blockedUser->blocked_at)->format('Y/m/d') }}</td>
                  <td>
                    {{ $blockedUser->unblocked_at ? \Morilog\Jalali\Jalalian::fromDateTime($blockedUser->unblocked_at)->format('Y/m/d') : '-' }}
                  </td>
                  <td>{{ $blockedUser->reason ?? 'بدون دلیل' }}</td>
                  <td>
                    <span
                      class="cursor-pointer fw-bold {{ $blockedUser->status == 1 ? 'text-danger' : 'text-success' }}"
                      title="برای تغییر وضعیت کلیک کنید" data-toggle="tooltip" data-status="{{ $blockedUser->status }}"
                      data-id="{{ $blockedUser->id }}" onclick="toggleStatus(this)">
                      {{ $blockedUser->status == 1 ? 'مسدود' : 'آزاد' }}
                    </span>
                  </td>
                  <td>
                    <button class="rounded-circle btn btn-light btn-sm delete-user-btn">
                      <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="Delete">
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- مودال افزودن کاربر -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-radius-6">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">افزودن کاربر مسدود</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="بستن">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="addUserForm" method="POST">
            @csrf
            <div class="form-group position-relative">
              <label class="label-top-input-special-takhasos" for="userMobile">شماره موبایل</label>
              <input type="text" name="mobile" id="userMobile" class="form-control h-50 mb-3"
                placeholder="09123456789">
            </div>
            <div class="form-group position-relative">
              <label class="label-top-input-special-takhasos" for="startDate">تاریخ شروع مسدودیت</label>
              <input type="text" id="startDate" name="blocked_at" class="form-control h-50 mb-3"
                placeholder="1403/01/01" data-jdp>
            </div>
            <div class="form-group position-relative">
              <label class="label-top-input-special-takhasos" for="endDate">تاریخ پایان مسدودیت</label>
              <input type="text" id="endDate" name="unblocked_at" class="form-control h-50 mb-3"
                placeholder="1403/01/10" data-jdp>
            </div>
            <div class="form-group position-relative">
              <textarea id="reason" name="reason" class="form-control h-50 mb-3" placeholder="دلیل مسدودیت را وارد کنید"></textarea>
            </div>
            <div class="mt-2 w-100">
              <button id="saveBlockedUserBtn" type="submit"
                class="btn my-btn-primary w-100 h-50 d-flex justify-content-center align-items-center">
                <span class="button_text">ثبت</span>
                <div class="loader" style="display: none;"></div>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('dr-assets/panel/jalali-datepicker/run-jalali.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/dr-panel.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/workhours/workhours.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/vacation/vacation.js') }}"></script>
<script>


  // ثبت کاربر مسدود
  $('#addUserForm').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serializeArray();
    const selectedClinicId = localStorage.getItem('selectedClinicId') || 'default';
    formData.push({
      name: 'selectedClinicId',
      value: selectedClinicId
    });

    const button = form.find('button[type="submit"]');
    const loader = button.find('.loader');
    const buttonText = button.find('.button_text');

    button.prop('disabled', true);
    buttonText.hide();
    loader.show();

    $.ajax({
      url: "{{ route('doctor-blocking-users.store') }}",
      method: "POST",
      data: $.param(formData),
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      success: function(response) {
        if (response.success) {
          toastr.success(response.message);
          appendBlockedUser(response.blocking_user);
          form[0].reset();
          $('#addUserModal').modal('hide');
        }
      },
      error: function(xhr) {
        const response = xhr.responseJSON;
        toastr.error(response.error);
        if (xhr.status === 422 && response.errors) {
          for (const field in response.errors) {
            toastr.error(response.errors[field][0]);
          }
        } else if (response && response.message) {
          toastr.error(response.message);
        } else {
          toastr.error("خطا در ثبت کاربر مسدود!");
        }
      },
      complete: function() {
        button.prop('disabled', false);
        buttonText.show();
        loader.hide();
      }
    });
  });

  // اضافه کردن کاربر به جدول
  function appendBlockedUser(user) {
    const tableBody = $('#blockedUsersTable tbody');
    const statusText = user.status == 1 ? 'مسدود' : 'آزاد';
    const statusClass = user.status == 1 ? 'text-danger' : 'text-success';

    // تبدیل تاریخ‌های میلادی به جلالی
    const blockedAt = moment(user.blocked_at).locale('fa').format('jYYYY/jMM/jDD');
    const unblockedAt = user.unblocked_at ? moment(user.unblocked_at).locale('fa').format('jYYYY/jMM/jDD') : '-';

    const newRow = `
        <tr data-id="${user.id}">
            <td>${user.user.first_name} ${user.user.last_name}</td>
            <td>${user.user.mobile}</td>
            <td>${blockedAt}</td>
            <td>${unblockedAt}</td>
            <td>${user.reason || 'بدون دلیل'}</td>
            <td>
                <span class="cursor-pointer fw-bold ${statusClass}" data-toggle="tooltip" data-status="${user.status}" data-id="${user.id}" onclick="toggleStatus(this)">
                    ${statusText}
                </span>
            </td>
            <td>
                <button class="rounded-circle btn btn-light btn-sm delete-user-btn">
                    <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="Delete">
                </button>
            </td>
        </tr>
    `;
    tableBody.append(newRow);
    $('[data-toggle="tooltip"]').tooltip();
  }

  // بارگذاری کاربران مسدود
  function loadBlockedUsers() {
    const selectedClinicId = localStorage.getItem('selectedClinicId') || 'default';
    $.ajax({
      url: "{{ route('doctor-blocking-users.index') }}",
      method: "GET",
      data: {
        selectedClinicId: selectedClinicId
      },
      success: function(response) {
        const tableBody = $('#blockedUsersTable tbody');
        tableBody.empty();
        if (response.blockedUsers.length === 0) {
          tableBody.append('<tr><td colspan="7" class="text-center">هیچ کاربر مسدودی یافت نشد.</td></tr>');
          return;
        }
        response.blockedUsers.forEach(user => {
          appendBlockedUser(user);
        });
      },
      error: function() {
        toastr.error("خطا در بارگذاری لیست کاربران!");
      }
    });
  }

  // حذف کاربر مسدود
  $(document).on('click', '#blockedUsersTable .delete-user-btn', function(e) {
    e.preventDefault();
    const row = $(this).closest('tr');
    const userId = row.data('id');
    const currentStatus = row.find('.cursor-pointer').data('status');

    let swalConfig = {
      title: 'آیا مطمئن هستید؟',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'بله، حذف شود!',
      cancelButtonText: 'لغو'
    };

    // اگر کاربر مسدود باشد، پیام تغییر وضعیت نمایش داده شود
    if (currentStatus === 1) {
      swalConfig.text = 'با حذف این آیتم، وضعیت کاربر به "آزاد" تغییر خواهد کرد. آیا می‌خواهید ادامه دهید؟';
      swalConfig.confirmButtonText = 'بله، ادامه بده!';
    } else {
      swalConfig.text = 'این کاربر برای همیشه از لیست مسدودیت حذف خواهد شد!';
    }

    Swal.fire(swalConfig).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('doctor-blocking-users.destroy', ['id' => ':userId']) }}".replace(':userId',
            userId),
          method: 'DELETE',
          data: {
            selectedClinicId: localStorage.getItem('selectedClinicId'),
            update_status: currentStatus === 1 ? true : false, // فقط برای کاربران مسدود تغییر وضعیت
            new_status: 0 // اگر تغییر وضعیت باشد، به آزاد تغییر کند
          },
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success) {
              toastr.success(response.message);
              row.remove();
            } else {
              toastr.error(response.message);
            }
          },
          error: function(xhr) {
            const response = xhr.responseJSON;
            toastr.error(response?.message || 'خطا در حذف کاربر!');
          }
        });
      }
    });
  });

  // تغییر وضعیت کاربر
  function toggleStatus(element) {
    const userId = $(element).data('id');
    const currentStatus = $(element).data('status');
    const newStatus = currentStatus === 1 ? 0 : 1;
    const statusText = newStatus === 1 ? 'مسدود' : 'آزاد';

    Swal.fire({
      title: 'تغییر وضعیت',
      text: `آیا می‌خواهید وضعیت این کاربر را به "${statusText}" تغییر دهید؟`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'بله، تغییر بده',
      cancelButtonText: 'لغو',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('doctor-blocking-users.update-status') }}",
          method: "PATCH",
          data: {
            _token: '{{ csrf_token() }}',
            selectedClinicId: localStorage.getItem('selectedClinicId'),
            id: userId,
            status: newStatus,
          },
          success: function(response) {
            if (response.success) {
              $(element)
                .removeClass('text-danger text-success')
                .addClass(newStatus === 1 ? 'text-danger' : 'text-success')
                .text(statusText);
              $(element).data('status', newStatus);
              toastr.success(response.message);
            } else {
              toastr.error(response.message);
            }
          },
          error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'خطا در تغییر وضعیت!');
          }
        });
      }
    });
  }

  // حذف پیام
  function deleteMessage(messageId, element) {
    Swal.fire({
      title: 'آیا مطمئن هستید؟',
      text: 'این پیام برای همیشه حذف خواهد شد!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'بله، حذف کن',
      cancelButtonText: 'لغو',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('doctor-blocking-users.delete-message', ':id') }}".replace(':id', messageId),
          method: "DELETE",
          data: {
            selectedClinicId: localStorage.getItem('selectedClinicId')
          },
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success) {
              $(element).closest('tr').remove();
              toastr.success(response.message);
            } else {
              toastr.error(response.message);
            }
          },
          error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'خطا در حذف پیام!');
          }
        });
      }
    });
  }

  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>
@endsection
