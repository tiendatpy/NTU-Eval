@extends('admin.template.show-template')

@section('title', 'Chi tiết đơn vị')

@php
$editRoute = route('admin.units.edit', $unit);
$backRoute = route('admin.units.index');
@endphp

@section('detail-content')
<div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Thông tin cơ bản -->
        <div class="col-span-2">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin cơ bản</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Tên đơn vị</p>
            <p class="font-medium">{{ $unit->name }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Loại đơn vị</p>
            <p class="font-medium">{{ $unit->type->name ?? 'Chưa phân loại' }}</p>
        </div>
        
        
        <!-- Thông tin hệ thống -->
        <div class="col-span-2 mt-4">
            <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Thông tin hệ thống</h3>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Mã đơn vị</p>
            <p class="font-medium">{{ $unit->id }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Số lượng thành viên</p>
            <p class="font-medium">{{ $totalUsers }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Ngày tạo</p>
            <p class="font-medium">{{ format_date($unit->created_at, 'd/m/Y', true) }}</p>
        </div>
        
        <div>
            <p class="text-sm text-gray-500 mb-1">Cập nhật gần nhất</p>
            <p class="font-medium">{{ format_date($unit->updated_at, 'd/m/Y', true) }}</p>
        </div>
    </div>
    
    <!-- Danh sách thành viên -->
    @if($totalUsers > 0)
    <div class="mt-8">
        <h3 class="text-base font-semibold mb-4 pb-2 border-b border-gray-200">Danh sách thành viên ({{ $totalUsers }})</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mã cán bộ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Họ tên
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vai trò
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thao tác
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->full_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->role->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-500 hover:text-blue-700">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
    @endif
    
    <!-- Nút xóa đơn vị -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn vị này?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-tertiary">
                Xóa đơn vị
            </button>
        </form>
    </div>
</div>
@endsection