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
        <div class="profile text-sm flex items-center gap-3">
          <span class="border-r-2 border-primary-600 pr-3" >Tài khoản</span>
          <div class="profile-info">
            <button class="profile-button block hover:text-states-400 hover:underline font-medium text-gray-800 focus:outline-none">
              {{ Auth::user()->full_name }}
            </button>
          </div>
          <div class="wrap-logout-btn">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" title="Đăng xuất" class="logout-btn flex w-full text-2xl text-secondary-600 hover:text-secondary-600/50">
                <span class="icomoon icon-external-link"></span>
              </button>
            </form>
          </div>
        </div>
        <div class="overlay js-popup fixed inset-0 bg-black/25 hidden">
          <!-- Popup -->
          <div class="profile-popup absolute min-w-[400px] lg:w-[600px] transform-center-middle bg-states-400 shadow-slate-600 rounded-lg p-4 z-50">
            <table class="w-full">
              <tr class="hover:bg-states-400">
                <td class="font-semibold p-5 text-white">Thông tin người dùng</td>
                <td class="p-5 text-right text-2xl" >
                  <button class="hover:text-white close-popup"><span class="icomoon icon-close"></span></button>
                </td>
              </tr>
            </table>
            <div class="bg-white p-4">
              <fieldset class="border border-gray-300 p-4 rounded-md">
                <legend class="text-lg">Thông tin cá nhân</legend>
                <table class="mx-auto profile-details">
                    <tr>
                      <td class="font-semibold text-right">Họ và tên:</td>
                      <td>{{ Auth::user()->full_name }}</td>
                    </tr>
                    <tr>
                      <td class="font-semibold text-right">Ngày sinh:</td>
                      <td>{{ format_date(Auth::user()->date_of_birth) }}</td>
                    </tr>
                    <tr>
                      <td class="font-semibold text-right">Email:</td>
                      <td>{{ Auth::user()->email }}</td>
                    </tr>
                    <tr>
                      <td class="font-semibold text-right">Số điện thoại:</td>
                      <td>{{ Auth::user()->phone ?? 'No phone number' }}</td>
                    </tr>
                    <tr>
                      <td class="font-semibold text-right">Đơn vị:</td>
                      <td>{{ Auth::user()->unit->name ?? 'None' }}</td>
                    </tr>
                    <tr>
                      <td class="font-semibold text-right">Vai trò:</td>
                      <td>{{ Auth::user()->role->name }}</td>
                </table>
              </fieldset>
            </div>
              <div class="mt-4 text-center">
                <a href="{{ route('profile.edit') }}" class="inline-block text-white font-medium hover:underline">
                  Đổi mật khẩu
                </a>
              </div>
            </div>
        </div>
        </div>
      @endauth
    </div>
  </div>
</header>