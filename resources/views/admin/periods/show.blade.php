@extends('admin.template.show-template')

@section('title', 'Chi tiết đợt đánh giá')

@php
$editRoute = route('admin.periods.edit', $period);
$backRoute = route('admin.periods.index');
@endphp

@section('detail-content')
<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Thông tin cơ bản -->
        <div class="col-span-2">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin cơ bản</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Năm học</p>
            <p class="font-medium">{{ $period->year }} - {{ $period->year + 1 }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Thời gian đánh giá</p>
            <p class="font-medium">{{ format_date($period->start_date, 'd/m/Y') }} - {{ format_date($period->end_date, 'd/m/Y') }} </p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Trạng thái</p>
            <p class="font-medium">
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                    {{ $period->status->name }}
                </span>
            </p>
        </div>
        
        
        <!-- Thông tin hệ thống -->
        <div class="col-span-2 mt-4">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin hệ thống</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">ID đợt đánh giá</p>
            <p class="font-medium">{{ $period->id }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Người tạo</p>
            <p class="font-medium">{{ $period->createdBy->full_name ?? 'Không xác định' }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Ngày tạo</p>
            <p class="font-medium">{{ format_date($period->created_at, 'd/m/Y', true) }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Cập nhật gần nhất</p>
            <p class="font-medium">{{ format_date($period->updated_at, 'd/m/Y', true) }}</p>
        </div>
    </div>
    
    <!-- Các action đặc biệt -->
    <div class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex flex-wrap gap-3">
            <!-- Nút xóa đợt đánh giá -->
            <form action="{{ route('admin.periods.destroy', $period) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-tertiary" onclick="return confirm('Bạn có chắc chắn muốn xóa đợt đánh giá này?')">
                    <span class="icomoon icon-trash-2 mr-2"></span> Xóa đợt đánh giá
                </button>
            </form>
        </div>
    </div>
</div>
@endsection