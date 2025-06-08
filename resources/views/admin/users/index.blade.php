@extends('layouts.app')

@section('content')
<section class="bg-white p-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-base mb-0">Quản lý tài khoản</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-secondary__v2 flex items-center">
            <span class="icomoon icon-plus mr-2 text-xl flex"></span> Thêm mới
        </a>
    </div>

    <div class="mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col down_lg:items-end lg:flex-wrap lg:flex-row items-center gap-4 ">
            <div class="flex-1 down_lg:w-full">
                <input name="search" value="{{ $search ?? '' }}" type="text" class="h-[45px] bg-primary-050 rounded-2xl border-1 w-full lg:w-[402px] px-7 py-6 text-sm" placeholder="Tìm kiếm theo tên, email, mã...">
            </div>
            <div class="w-auto down_lg:w-full">
                <select name="role" class="border rounded-md down_lg:w-full">
                    <option value="">-- Tất cả vai trò --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ isset($roleFilter) && $roleFilter == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-auto down_lg:w-full">
                <select name="unit" class="border rounded-md down_lg:w-full">
                    <option value="">-- Tất cả đơn vị --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ isset($unitFilter) && $unitFilter == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="wrap-button flex items-center gap-4">
                <button type="submit" class="btn btn-primary flex items-center ">
                    <span class="icomoon icon-search mr-2 flex"></span> Tìm kiếm
                </button>
                <a href="{{ route('admin.users.index') }}" class=" btn btn-additional flex items-center">
                    <span class="icomoon icon-refresh mr-2 flex"></span> Đặt lại
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-states-300">
                <tr>
                    <th class=" text-left">Mã</th>
                    <th class=" text-left">Họ tên</th>
                    <th class=" text-left">Email</th>
                    <th class=" text-left">Vai trò</th>
                    <th class=" text-left">Đơn vị</th>
                    <th class=" text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b ">
                    <td class="">{{ $user->id }}</td>
                    <td class="">{{ $user->full_name }}</td>
                    <td class="">{{ $user->email }}</td>
                    <td class="">{{ $user->role->name }}</td>
                    <td class="">{{ $user->unit->name }}</td>
                    <td class=" text-center">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-blue-500 hover:text-blue-700 mx-1">
                            <span class="icomoon icon-eye text-xl"></span>
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-500 hover:text-yellow-700 mx-1">
                            <span class="icomoon icon-pencil-alt text-xl"></span>
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 mx-1" 
                                onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                <span class="icomoon icon-trash"></span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class=" text-center">Không tìm thấy tài khoản nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</section>
@endsection