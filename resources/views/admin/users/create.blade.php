@extends('admin.template.create-template')

@section('title', 'Thêm mới tài khoản')

@php
$route = route('admin.users.store');
$cancelRoute = route('admin.users.index');
@endphp

@section('form-fields')
    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3 ">
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3 ">
        </div>

        <div>
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh <span class="text-red-500">*</span></label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3 ">
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
            <input type="password" name="password" id="password" required
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3 ">
            <p class="text-xs text-gray-500 mt-1">Mật khẩu phải có ít nhất 8 ký tự</p>
        </div>
        
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3 ">
        </div>
        
        <div>
            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-1">Vai trò <span class="text-red-500">*</span></label>
            <select name="role_id" id="role_id" required
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3 ">
                <option value="">-- Chọn vai trò --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-1">Đơn vị <span class="text-red-500">*</span></label>
            <select name="unit_id" id="unit_id" required
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3 ">
                <option value="">-- Chọn đơn vị --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="education_id" class="block text-sm font-medium text-gray-700 mb-1">Trình độ học vấn <span class="text-red-500">*</span></label>
            <select name="education_id" id="education_id" required
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3 ">
                <option value="">-- Chọn trình độ học vấn --</option>
                @foreach($educationLevels as $education)
                    <option value="{{ $education->id }}" {{ old('education_id') == $education->id ? 'selected' : '' }}>
                        {{ $education->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="mt-4 pl-1">
        <p class="text-red-500 text-xs">(*) Thông tin bắt buộc</p>
    </div>
@endsection