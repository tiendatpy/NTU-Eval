@extends('admin.template.show-template')

@section('title', 'Chi tiết tiêu chí đánh giá')

@php
$editRoute = route('admin.criteria.edit', $criterion);
$backRoute = route('admin.criteria.index');
@endphp

@section('detail-content')
<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Thông tin cơ bản -->
        <div class="col-span-2">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin cơ bản</h3>
        </div>
        
        <div class="col-span-2">
            <p class="text-sm text-gray-500 mb-1">Tên tiêu chí</p>
            <p class="font-medium">{{ $criterion->name }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Danh mục</p>
            <p class="font-medium">
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                    {{ $criterion->category->name ?? 'Chưa phân loại' }}
                </span>
            </p>
        </div>
        
        
        <!-- Thông tin hệ thống -->
        <div class="col-span-2 mt-4">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin hệ thống</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">ID tiêu chí</p>
            <p class="font-medium">{{ $criterion->id }}</p>
        </div>
    
        <div>
            <p class="text-sm text-gray-500 mb-1">Ngày tạo</p>
            <p class="font-medium">{{ format_date($criterion->created_at, 'd/m/Y', true) }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Cập nhật gần nhất</p>
            <p class="font-medium">{{ format_date($criterion->updated_at, 'd/m/Y', true) }}</p>
        </div>
    </div>
    
    <!-- Nút xóa tiêu chí -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <form action="{{ route('admin.criteria.destroy', $criterion) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tiêu chí đánh giá này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-tertiary">
                Xóa tiêu chí
            </button>
        </form>
    </div>
</div>
@endsection