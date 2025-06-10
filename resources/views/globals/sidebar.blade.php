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
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('admin.periods.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-calendar text-2xl"></span>
            <a class="block w-full" href="{{ route('admin.periods.index') }}">
                <span>Đợt đánh giá</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('admin.users.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-users text-2xl"></span>
            <a class="block w-full" href="{{ route('admin.users.index') }}">
                <span>Tài khoản</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('admin.units.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-color-swatch text-2xl"></span>
            <a class="block w-full" href="{{ route('admin.units.index') }}">
                <span>Đơn vị</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('admin.criteria.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-table text-2xl"></span>
            <a class="block w-full" href="{{ route('admin.criteria.index') }}">
                <span>Tiêu chí đánh giá</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('admin.rewards.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-gift text-2xl"></span>
            <a class="block w-full" href="{{ route('admin.rewards.index') }}">
                <span>Hình thức khen thưởng</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('admin.quality.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-star text-2xl"></span>
            <a class="block w-full" href="{{ route('admin.quality.index') }}">
                <span>Xếp loại chất lượng</span>
            </a>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('admin.titles.*') ? 'active-menu text-states-600' : '' }} ">
            <span class="icomoon icon-tag text-2xl"></span>
            <a class="block w-full" href="{{ route('admin.titles.index') }}">
                <span>Danh hiệu thi đua</span>
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