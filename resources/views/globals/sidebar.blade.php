<aside class="bg-white relative left-0 bottom-0 w-20p down_lg:hidden xl:text-lg">
    <div class="menu px-6 py-8">
        <ul class="menu-list mb-11">
            <li class="px-4 mb-11 font-medium hover:text-states-500 cursor-pointer">
            </li>
            <li class="px-4 mb-11 font-medium hover:text-states-500 cursor-pointer">
                <span>Khen thưởng</span>
            </li>
            <li class="px-4 mb-11 font-medium hover:text-states-500 cursor-pointer has-menu-item">
                <div class="flex items-center gap-8">
                    <span class="icomoon icon-folder text-2xl"></span>
                    <span>Đánh giá & thi đua</span>
                </div>
                <ul class="sub-menu-list pl-24 mt-3 last-mb-none text-black">
                    <li class="sub-menu-item hover:text-states-400 mb-5">
                        <a class="block" href="{{ route('evaluations.index')}}"><span>Tự đánh giá</span></a>
                    </li>
                    <li class="sub-menu-item hover:text-states-400 mb-5">
                        <a class="block" href="{{ route('title-nominations.index') }}"><span>Thi đua</span></a>
                    </li>
                </ul>
            </li>
            <li class="px-4 mb-11 font-medium hover:text-states-500 cursor-pointer has-menu-item">
                <span class="flex items-center gap-8">
                    <span class="icomoon icon-table text-2xl"></span>
                    <span>Danh sách đánh giá</span>
                </span>
                <ul class="sub-menu-list pl-24 mt-3 last-mb-none text-black">
                    <li class="sub-menu-item hover:text-states-400 mb-5">
                        <a class="block" href="{{ route('title-nominations.list') }}"><span>Đánh giá</span></a>
                    </li>
                    <li class="sub-menu-item hover:text-states-400 mb-5">
                        <a class="block" href=""><span>Danh hiệu thi đua</span></a>
                    </li>
                </ul>
            </li>
        </ul>
        <ul class="menu-list mb-11">
            <li class="px-4 mb-11 font-medium hover:text-states-500 cursor-pointer">
                <span>Docs</span>
            </li>
            <li class="px-4 mb-11 font-medium hover:text-states-500 cursor-pointer">
                <span>Components</span>
            </li>
            <li class="px-4 mb-11 font-medium hover:text-states-500 cursor-pointer">
                <span>Help</span>
            </li>
        </ul>
    </div>
</aside>