<div class="toast-container fixed bottom-5 right-8 z-50">
  @if (session('success'))
      <div class="toast success text-left">
        <span class="icomoon icon-check-circle text-xl"></span>
        <div>
            <h2 class="mb-0">Thành công</h2>
            <span class="text-primary-100">{{ session('success') }}</span>
        </div>
      </div>
  @endif

  @if (session('error'))
      <div class="toast error">
        <h2>Thất bại</h2>
        {{ session('error') }}
      </div>
  @endif
</div>