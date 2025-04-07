<header class="bg-white h-32 fixed top-0 left-0 right-0 z-50">
  <div class="px-10 flex h-full justify-between items-center">
    <div class="header-left md:flex md:items-center md:gap-10 xl:gap-16">
      <div class="logo down_md:hidden">
        <a class="text-[36px] flex" href="#">
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
      <div class="notification flex">
        <span class="icomoon icon-bell text-2xl"></span>
      </div>
      <div class="profile">
        <div class="profile-avt overflow-hidden">
          <button class="flex"><img class="rounded-full h-16 w-16" src="{{ asset('images/user.jpg') }}" alt="User Avatar"></button>
        </div>
      </div>
    </div>
  </div>
</header>