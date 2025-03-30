<div class="container-fluid py-4" dir="rtl">
  <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
    <div
      class="card-header bg-gradient-primary text-white p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          class="animate-bounce">
          <path d="M5 12h14M12 5l7 7-7 7" />
        </svg>
        <h5 class="mb-0 fw-bold">مدارک من</h5>
      </div>
      <a href="{{ route('dr-clinic-management') }}"
        class="btn btn-outline-light btn-sm rounded-pill d-flex align-items-center gap-2 hover:shadow-md transition-all">
        <svg width="16" style="transform: rotate(180deg)" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2">
          <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        بازگشت
      </a>
    </div>

    <div class="card-body p-4">
      <div class="row g-4">
        <div class="col-12">
          <div class="bg-light p-3 rounded-3 shadow-sm hover:shadow-md transition-all">
            <label class="form-label fw-bold text-dark mb-2">آپلود مدارک</label>
            <input type="file" wire:model="files" multiple class="form-control input-shiny"
              accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
            @foreach ($files as $index => $file)
              <div class="mt-2">
                <input type="text" wire:model="titles.{{ $index }}" class="form-control input-shiny mt-1"
                  placeholder="توضیح مدرک {{ $index + 1 }}">
              </div>
            @endforeach
            <button wire:click="uploadFiles"
              class="btn btn-primary rounded-pill mt-3 px-4 d-flex align-items-center gap-2">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M12 5v14M5 12h14" />
              </svg>
              آپلود
            </button>
          </div>
        </div>

        <div class="col-12 mt-3">
          <h6 class="fw-bold mb-3">مدارک آپلودشده</h6>
          <div class="row g-3">
            @forelse ($documents as $document)
              <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm position-relative">
                  @if (str_starts_with($document->file_type, 'image'))
                    <img src="{{ Storage::url($document->file_path) }}" class="card-img-top"
                      alt="{{ $document->title ?? 'مدرک پزشک' }}" style="height: 150px; object-fit: cover;">
                  @else
                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                      style="height: 150px;">
                      <span class="text-muted">{{ pathinfo($document->file_path, PATHINFO_EXTENSION) }}</span>
                    </div>
                  @endif
                  <div class="card-body p-2 text-center">
                    <p class="text-muted small">{{ $document->title ?? 'بدون توضیح' }}</p>
                    <p class="small {{ $document->is_verified ? 'text-success' : 'text-warning' }}">
                      وضعیت: {{ $document->is_verified ? 'تأیید شده' : 'تأیید نشده' }}
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                      <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                        class="btn btn-sm btn-outline-info rounded-pill">
                        مشاهده
                      </a>
                      <button wire:click="deleteFile({{ $document->id }})"
                        class="btn btn-sm btn-outline-danger rounded-pill">
                        <svg style="transform: rotate(180deg)" width="16" height="16" viewBox="0 0 24 24"
                          fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12 text-center py-5">
                <p class="text-muted">هیچ مدرکی آپلود نشده است.</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('livewire:init', function() {
      Livewire.on('show-alert', (event) => toastr[event.type](event.message));
    });
  </script>
</div>
