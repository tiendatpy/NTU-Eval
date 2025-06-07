@php
 $isUnitLeader = auth()->user()->role->isUnitLeader == true;
 $isAdmin = auth()->user()->role->isSuperAdmin == true;
@endphp
<div class="menu px-6 py-8">
    <ul class="menu-list mb-11">
        @if($isUnitLeader)
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 ">
            <span class="icomoon icon-chart-pie text-2xl"></span>
            <a class="block w-full" href="{{ route('dashboard.stats') }}">
                <span>Tổng quan</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer has-menu-item">
            <span class="flex items-center gap-8 ">
                <span class="icomoon icon-pencil text-2xl"></span>
                <span>Tự đánh giá</span>
            </span>
            <ul class="sub-menu-list pl-24 mt-3 last-mb-none text-black {{ request()->routeIs('evaluations.index') || request()->routeIs('unit.evaluations.index') ? 'show-submenu' : '' }} ">
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block {{ request()->routeIs('evaluations.index') ? 'text-states-600 font-medium' : '' }}" href="{{ route('evaluations.index')}}">
                        <span>Cá nhân</span>
                    </a>
                </li>
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block {{ request()->routeIs('unit.evaluations.index') ? 'text-states-600 font-medium' : '' }}" href="{{ route('unit.evaluations.index') }}">
                        <span>Đơn vị</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer has-menu-item">
            <span class="flex items-center gap-8 ">
                <span class="icomoon icon-clipboard-check text-2xl"></span>
                <span>Phê duyệt</span>
            </span>
            <ul class="sub-menu-list pl-24 mt-3 last-mb-none text-black {{ request()->routeIs('evaluations.list') || request()->routeIs('unit-evaluations.approve') ? 'show-submenu' : '' }}">
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block {{ request()->routeIs('evaluations.list') ? 'text-states-600 font-medium' : '' }}" href="{{ route('evaluations.list')}}">
                        <span>Cá nhân</span>
                    </a>
                </li>
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block {{ request()->routeIs('unit-evaluations.approve') ? 'text-states-600 font-medium' : '' }}" href="{{ route('unit-evaluations.approve') }}">
                        <span>Đơn vị</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('unit.members') ? 'active-menu text-states-600' : '' }}">
            <span class="icomoon icon-users text-2xl"></span>
            <a class="block w-full" href="{{ route('unit.members') }}">
                <span>Thành viên đơn vị</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer has-menu-item">
            <span class="flex items-center gap-8 ">
                <span class="icomoon icon-document-text text-2xl"></span>
                <span>Báo cáo</span>
            </span>
            <ul class="sub-menu-list pl-24 mt-3 last-mb-none text-black {{ request()->routeIs('unit-report') || request()->routeIs('last-report') ? 'show-submenu' : '' }}">
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block {{ request()->routeIs('unit-report') ? 'text-states-600 font-medium' : '' }}" href="{{ route('unit-report') }}">
                        <span>Báo cáo sơ kết</span>
                    </a>
                </li>
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block {{ request()->routeIs('last-report') ? 'text-states-600 font-medium' : '' }}" href="{{ route('last-report') }}">
                        <span>Tờ trình</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('periods.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-cog text-2xl"></span>
            <a class="block w-full" href="{{ route('periods.index') }}">
                <span>Quản lý đợt đánh giá</span>
            </a>
        </li>

        @elseif($isAdmin)
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('periods.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-clock text-2xl"></span>
            <a class="block w-full" href="{{ route('periods.index') }}">
                <span>Quản lý đợt đánh giá</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('periods.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-users text-2xl"></span>
            <a class="block w-full" href="{{ route('periods.index') }}">
                <span>Quản lý tài khoản</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('periods.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-color-swatch text-2xl"></span>
            <a class="block w-full" href="{{ route('periods.index') }}">
                <span>Quản lý đơn vị</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('periods.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-table text-2xl"></span>
            <a class="block w-full" href="{{ route('periods.index') }}">
                <span>Quản lý tiêu chí đánh giá</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('periods.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-gift text-2xl"></span>
            <a class="block w-full" href="{{ route('periods.index') }}">
                <span>Quản lý hình thức khen thưởng</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('periods.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-star text-2xl"></span>
            <a class="block w-full" href="{{ route('periods.index') }}">
                <span>Quản lý xếp loại chất lượng</span>
            </a>
        </li>
        
        @else
            <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('evaluations.index') || request()->routeIs('evaluations.result')  ? 'active-menu text-states-600' : '' }}">
                <span class="icomoon icon-pencil text-2xl"></span>
                <a class="block w-full" href="{{ route('evaluations.index') }}">
                    <span>Tự đánh giá</span>
                </a>
            </li>
            <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('evaluations.list') || request()->routeIs('all-evaluations.view-details') ? 'active-menu text-states-600' : '' }}">
                <span class="icomoon icon-document-search text-2xl"></span>
                <a class="block w-full" href="{{ route('evaluations.list')}}">
                    <span>Danh sách đánh giá</span>
                </a>
            </li>
        @endif
    </ul>
    <ul class="menu-list mb-11">
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer">
            <span>Tài liệu</span>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer">
            <span>Trợ giúp</span>
        </li>
    </ul>
</div>