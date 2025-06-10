@extends('admin.template.create-template')

@section('title', 'Thêm mới danh hiệu thi đua')

@php
$route = route('admin.titles.store');
$cancelRoute = route('admin.titles.index');
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
        <div class="md:col-span-2">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên danh hiệu <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
        </div>
        
        <div class="md:col-span-2">
            <label for="type_id" class="block text-sm font-medium text-gray-700 mb-1">Loại danh hiệu <span class="text-red-500">*</span></label>
            <select name="type_id" id="type_id" required
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
                <option value="">-- Chọn loại danh hiệu --</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <textarea name="description" id="description" rows="4"
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">{{ old('description') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Mô tả chi tiết về tiêu chuẩn của danh hiệu thi đua này</p>
        </div>
    </div>
    
    <div class="mt-4 pl-1">
        <p class="text-red-500 text-xs">(*) Thông tin bắt buộc</p>
    </div>
@endsection