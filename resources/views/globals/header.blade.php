<header class="header bg-white h-32 fixed top-0 left-0 right-0 z-50">
  <div class="px-10 flex h-full justify-between items-center">
    <div class="header-left md:flex md:items-center md:gap-10 xl:gap-16">
      <div class="logo down_md:hidden">
        <a class="text-[36px] flex" href="{{ route('dashboard') }}">
          <span class="icomoon icon-logo"></span>
        </a>
      </div>
      <div class="header-search down_xl:hidden flex items-center">
        <input type="text" class="h-[45px] rounded-2xl border-1 w-[402px] px-7 py-6 text-sm" placeholder="Search">
      </div>
      <div class="hamburger-menu xl:hidden">
        <button class="flex">
          <span class="icomoon icon-menu-alt-1 text-h3"></span>
        </button>
      </div>
    </div> 

    <div class="header-right flex items-center gap-5">
      <!-- Notification -->
      <div class="notification flex">
        <span class="icomoon icon-bell text-2xl"></span>
      </div>

      <!-- User Profile -->
      @auth
        <div class="profile flex items-center gap-3">
          <div class="profile-avt overflow-hidden">
            <button class="flex">
              <img class="rounded-full h-16 w-16" src="{{ asset('images/user.jpg') }}" alt="User Avatar">
            </button>
          </div>
          <div class="profile-info">
            <button id="profile-button" class="block font-medium text-gray-800 focus:outline-none">
              {{ Auth::user()->full_name }}
            </button>
          </div>
          <div class="logout-btn">
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
              @csrf
              <button class="block w-full text-center text-red-600 hover:underline" onclick="event.preventDefault(); this.closest('form').submit();">
                Log Out
              </button>
            </form>
          </div>

          <!-- Popup -->
          </div>
          <div id="profile-popup" class="absolute top-0 left-0 bg-white shadow-lg rounded-lg w-64 p-4 z-50">
            <div class="flex flex-col items-center">
              <img class="rounded-full h-20 w-20 mb-3" src="{{ asset('images/user.jpg') }}" alt="User Avatar">
              <h3 class="text-lg font-semibold">{{ Auth::user()->full_name }}</h3>
              <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
              <p class="text-sm text-gray-500">{{ Auth::user()->phone ?? 'No phone number' }}</p>
              <p class="text-sm text-gray-500">Role: {{ Auth::user()->role->name }}</p>
              <p class="text-sm text-gray-500">Unit: {{ Auth::user()->unit->name ?? 'None' }}</p>
            </div>
            <div class="mt-4">
              <a href="{{ route('profile.edit') }}" class="block text-center text-blue-600 hover:underline">
                Edit Profile
              </a>
            </div>
          </div>
        </div>
      @endauth
    </div>
  </div>
</header>