@extends('admin.template.show-template')

@section('title', 'Chi tiết xếp loại chất lượng')

@php
$editRoute = route('admin.quality.edit', $quality);
$backRoute = route('admin.quality.index');
@endphp

@section('detail-content')
<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Thông tin cơ bản -->
        <div class="col-span-2">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin cơ bản</h3>
        </div>
        
        <div class="col-span-2">
            <p class="text-sm text-gray-500 mb-1">Tên xếp loại</p>
            <p class="font-medium">{{ $quality->name }}</p>
        </div>
        
        <div class="col-span-2">
            <p class="text-sm text-gray-500 mb-1">Mô tả</p>
            <p class="font-medium">{{ $quality->description ?? 'Không có mô tả' }}</p>
        </div>
        
        
        <!-- Thông tin hệ thống -->
        <div class="col-span-2 mt-4">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin hệ thống</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">ID xếp loại</p>
            <p class="font-medium">{{ $quality->id }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Ngày tạo</p>
            <p class="font-medium">{{ format_date($quality->created_at, 'd/m/Y', true) }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Cập nhật gần nhất</p>
            <p class="font-medium">{{ format_date($quality->updated_at, 'd/m/Y', true) }}</p>
        </div>
    </div>
    
    <!-- Nút xóa xếp loại -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <form action="{{ route('admin.quality.destroy', $quality) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa xếp loại chất lượng này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-tertiary">
                Xóa xếp loại
            </button>
        </form>
    </div>
</div>
@endsection