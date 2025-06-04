<!-- filepath: e:\WORKSPACE\ntu-eval\resources\views\pages\unit-leader\unit-members.blade.php -->
@extends('layouts.app')

@section('title', 'Danh Sách Thành Viên Đơn Vị')

@section('content')
<section class="mod-unit-members bg-white rounded-2xl py-8">
    <div class="container">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base mb-0">Danh Sách Thành Viên {{ auth()->user()->unit->name }}</h2>
            <span class="text-primary-700">Năm học: {{ $currentYear }} - {{ $currentYear + 1 }}</span>
        </div>

        <!-- Form tìm kiếm và lọc -->
        <div class="mb-6">
            <form action="{{ route('unit.members') }}" method="GET" class="flex flex-col md:flex-row md:items-center md:justify-end gap-4">
                <div class="">
                    <div class="header-search flex items-center">
                        <input name="search_name" value="{{ $searchName ?? '' }}" type="text" class="h-[45px] bg-primary-050 rounded-2xl border-1 w-[402px] px-7 py-6 text-sm" placeholder="Tìm kiếm theo họ tên...">
                    </div>
                </div>
                <div class="w-auto">
                    <select id="evaluation_status" name="evaluation_status" 
                        class="mt-1">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="evaluated" {{ isset($evaluationStatus) && $evaluationStatus == 'evaluated' ? 'selected' : '' }}>Đã đánh giá</option>
                        <option value="not_evaluated" {{ isset($evaluationStatus) && $evaluationStatus == 'not_evaluated' ? 'selected' : '' }}>Chưa đánh giá</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span class="icomoon icon-search mr-1"></span> Lọc
                    </button>
                    <a href="{{ route('unit.members') }}" class="btn btn-additional">
                        <span class="icomoon icon-refresh-ccw mr-1"></span> Đặt lại
                    </a>
                </div>
            </form>
        </div>

        <!-- Hiển thị thông tin lọc nếu có -->
        @if($searchName || $evaluationStatus)
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
            <div class="flex">
                <span class="icomoon icon-filter mr-2"></span>
                <p class="mb-0">
                    Đang lọc: 
                    @if($searchName)
                        <span class="font-medium">Họ tên chứa "{{ $searchName }}"</span>
                    @endif
                    
                    @if($searchName && $evaluationStatus)
                        và Trạng thái đánh giá:
                    @endif
                    
                    @if($evaluationStatus)
                        <span class="font-medium">
                            Trạng thái đánh giá:
                            {{ $evaluationStatus == 'evaluated' ? 'Đã đánh giá' : 'Chưa đánh giá' }}
                        </span>
                    @endif
                </p>
            </div>
        </div>
        @endif

        <!-- Thêm phần thống kê - giữ nguyên -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-50 mr-4">
                        <span class="icomoon icon-users flex text-states-500"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tổng số thành viên</p>
                        <p class="text-xl font-bold text-states-500">{{ $totalMembers }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-50 mr-4">
                        <span class="icomoon icon-check-circle text-green-500 flex"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Đã đánh giá</p>
                        <p class="text-xl font-bold text-green-600">{{ $evaluatedCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-50 mr-4">
                        <span class="icomoon icon-x-circle text-red-500 flex"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Chưa đánh giá</p>
                        <p class="text-xl font-bold text-red-600">{{ $notEvaluatedCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-50 mr-4">
                        <span class="icomoon icon-chart-pie text-purple-500 flex"></span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tỉ lệ hoàn thành</p>
                        <p class="text-xl font-bold text-purple-600">{{ $evaluationPercentage }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thêm progress bar - giữ nguyên -->
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6 dark:bg-gray-700">
            <div class="bg-neutral-500 h-2.5 rounded-full" style="width: {{ $evaluationPercentage }}%"></div>
        </div>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-states-300">
                    <th class="text-left">STT</th>
                    <th class="text-left">Mã cán bộ</th>
                    <th class="text-left">Họ và tên</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Chức vụ</th>
                    <th class="text-left">Số điện thoại</th>
                    <th class="text-center">Trạng thái đánh giá</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                <tr class="border-b border-states-200 hover:bg-gray-50">
                    <td>{{ $members->firstItem() + $index }}</td>
                    <td>{{ $member->id }}</td>
                    <td class="font-medium">{{ $member->full_name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->role->name }}</td>
                    <td>{{ $member->phone}}</td>
                    <td class="text-center">
                        @if(in_array($member->id, $evaluatedUserIds ?? []))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Đã đánh giá
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Chưa đánh giá
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">Không có thành viên nào phù hợp với điều kiện tìm kiếm.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $members->links() }}
        </div>

        <!-- Thêm ghi chú thống kê - giữ nguyên -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Ghi chú:</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li class="flex items-center gap-2">
                    <span class="icomoon icon-check-circle text-green-500 flex"></span>
                    <span>Thành viên đã đánh giá: {{ $evaluatedCount }} người ({{ $evaluationPercentage }}%)</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="icomoon icon-x-circle text-red-500 flex"></span>
                    <span>Thành viên chưa đánh giá: {{ $notEvaluatedCount }} người ({{ 100 - $evaluationPercentage }}%)</span>
                </li>
                <li class="flex items-center mt-2">
                    <span class="text-xs text-gray-500">* Thống kê tính theo năm học {{ $currentYear }} - {{ $currentYear + 1 }}</span>
                </li>
            </ul>
        </div>
    </div>
</section>
@endsection