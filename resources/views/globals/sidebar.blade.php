@php
 $isUnitLeader = auth()->user() && auth()->user()->role->name === 'Trưởng đơn vị';
@endphp
<aside class="bg-white relative left-0 bottom-0 w-20p down_lg:hidden xl:text-lg">
    <div class="menu px-6 py-8">
        <ul class="menu-list mb-11">
            <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('evaluations.index') ? 'active-menu text-states-600' : '' }}">
                <span class="icomoon icon-pencil text-2xl"></span>
                <a class="block w-full" href="{{ route('evaluations.index')}}">
                    <span>Tự đánh giá</span>
                </a>
            </li>
            <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 {{ request()->routeIs('evaluation.results') ? 'active-menu' : '' }}">
                <span class="icomoon icon-folder text-2xl"></span>
                <a class="block w-full {{ request()->routeIs('evaluation.results') ? 'text-states-600' : '' }}" href="">
                    <span>Kết quả đánh giá</span>
                </a>
            </li>
            <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer has-menu-item
                {{ request()->routeIs('quality-ratings.list') || request()->routeIs('title-nominations.list') ? 'active-menu active-parent ' : '' }}">
                <span class="flex items-center gap-8 ">
                    <span class="icomoon icon-check-circle text-2xl"></span>
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
            @if($isUnitLeader)
            <li class="px-4 py-2 mb-11 font-medium hover:bg-primary-100 hover:rounded-xl cursor-pointer flex items-center gap-8 ">
                <span class="icomoon icon-document-text text-2xl"></span>
                <a class="block w-full" href="">
                    <span>Báo cáo tổng kết</span>
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
</aside>