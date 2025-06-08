@extends('admin.template.show-template')

@section('title', 'Chi tiết tài khoản')

@php
$editRoute = route('admin.users.edit', $user);
$backRoute = route('admin.users.index');
@endphp

@section('detail-content')
<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Thông tin cơ bản -->
        <div class="col-span-2">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin cơ bản</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Họ và tên</p>
            <p class="font-medium">{{ $user->full_name }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Email</p>
            <p class="font-medium">{{ $user->email }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Ngày sinh</p>
            <p class="font-medium">{{ $user->date_of_birth ? date('d/m/Y', strtotime($user->date_of_birth)) : 'Chưa cập nhật' }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Số điện thoại</p>
            <p class="font-medium">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
        </div>
        
        <!-- Thông tin đơn vị và vai trò -->
        <div class="col-span-2 mt-4">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin công việc</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Đơn vị</p>
            <p class="font-medium">{{ $user->unit->name }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Vai trò</p>
            <p class="font-medium">{{ $user->role->name }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Trình độ học vấn</p>
            <p class="font-medium">{{ $user->education->name ?? 'Chưa cập nhật' }}</p>
        </div>
        
        <!-- Thông tin hệ thống -->
        <div class="col-span-2 mt-4">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin hệ thống</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Mã cán bộ</p>
            <p class="font-medium">{{ $user->id }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Ngày tạo tài khoản</p>
            <p class="font-medium">{{ format_date($user->created_at, 'd/m/Y', true) }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Cập nhật gần nhất</p>
            <p class="font-medium">{{ format_date($user->updated_at, 'd/m/Y', true) }}</p>
        </div>
        
    </div>
    
    
    <!-- Nút xóa tài khoản -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-tertiary">
                Xóa tài khoản
            </button>
        </form>
    </div>
</div>
@endsection