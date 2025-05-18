@php
 $isUnitLeader = auth()->user()->role->name === 'Trưởng đơn vị';
@endphp
<div class="menu px-6 py-8">
    <ul class="menu-list mb-11">
        @if($isUnitLeader)
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 ">
            <span class="icomoon icon-chart-pie text-2xl"></span>
            <a class="block w-full" href="">
                <span>Tổng quan</span>
            </a>
        </li>
        @endif
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer {{ request()->routeIs('evaluations.index') ? 'active-menu text-states-600' : '' }} {{ $isUnitLeader ? 'has-menu-item' : 'flex items-center gap-8' }}">
            @if ($isUnitLeader)
            <span class="flex items-center gap-8 ">
                <span class="icomoon icon-pencil text-2xl"></span>
                <span>Tự đánh giá</span>
            </span>
            <ul class="sub-menu-list pl-24 mt-3 last-mb-none text-black ">
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block" href="{{ route('evaluations.index')}}">
                        <span>Cá nhân</span>
                    </a>
                </li>
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block " href="{{ route('unit.evaluations.index') }}">
                        <span>Đơn vị</span>
                    </a>
                </li>
            </ul>
            @else
            <span class="icomoon icon-pencil text-2xl"></span>
            <a class="block w-full" href="{{ route('evaluations.index')}}">
                <span>Tự đánh giá</span>
            </a>
            @endif
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer has-menu-item
            {{ request()->routeIs('quality-ratings.list') || request()->routeIs('title-nominations.list') ? 'active-menu active-parent ' : '' }}">
            <span class="flex items-center gap-8 ">
                <span class="icomoon icon-clipboard-check text-2xl"></span>
                @if($isUnitLeader)
                    <span>Phê duyệt</span>
                @else
                    <span>Danh sách đánh giá</span>
                @endif
            </span>
            <ul class="sub-menu-list pl-24 mt-3 last-mb-none text-black {{ request()->routeIs('quality-ratings.list') || request()->routeIs('title-nominations.list') ? 'show-submenu' : '' }}">
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block {{ request()->routeIs('quality-ratings.list') ? 'text-states-600 font-medium' : '' }}" href="{{ route('quality-ratings.list') }}">
                        <span>Xếp loại chất lượng</span>
                    </a>
                </li>
                <li class="sub-menu-item hover:text-states-400 mb-5">
                    <a class="block {{ request()->routeIs('title-nominations.list') ? 'text-states-600 font-medium' : '' }}" href="{{ route('title-nominations.list') }}">
                        <span>Danh hiệu thi đua</span>
                    </a>
                </li>
            </ul>
        </li>
        @if($isUnitLeader)
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('unit.members') ? 'active-menu text-states-600' : '' }}">
            <span class="icomoon icon-users text-2xl"></span>
            <a class="block w-full" href="{{ route('unit.members') }}">
                <span>Thành viên đơn vị</span>
            </a>
        </li>
        @endif
    </ul>
    <ul class="menu-list mb-11">
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer">
            <span>Docs</span>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer">
            <span>Components</span>
        </li>
        <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer">
            <span>Help</span>
        </li>
    </ul>
</div>