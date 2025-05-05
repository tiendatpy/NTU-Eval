<div class="toast-container fixed bottom-5 right-8 z-50">
  @if (session('success'))
      <div class="toast success text-left relative">
        <button class="close-toast absolute text-white transform-middle right-5 text-xl flex">
          <span class="icomoon icon-close"></span>
        </button>
        <span class="icomoon icon-check-circle text-xl"></span>
        <div>
            <h2 class="mb-0">Thành công</h2>
            <span class="text-primary-100">{{ session('success') }}</span>
        </div>
      </div>
  @endif

  @if (session('error'))
  <div class="toast error text-left relative">
    <button class="close-toast absolute text-white transform-middle right-5 text-xl flex">
      <span class="icomoon icon-close"></span>
    </button>
    <span class="icomoon icon-information-circle"></span>
    <div>
        <h2 class="mb-0">Thành công</h2>
        <span class="text-primary-100">{{ session('error') }}</span>
    </div>
  </div>
  @endif
</div>