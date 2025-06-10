@extends('admin.template.create-template')

@section('title', 'Thêm mới tiêu chí đánh giá')

@php
$route = route('admin.criteria.store');
$cancelRoute = route('admin.criteria.index');
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
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên tiêu chí <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
        </div>
        
        <div class="md:col-span-2">
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Danh mục tiêu chí <span class="text-red-500">*</span></label>
            <select name="category_id" id="category_id" required
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="mt-4 pl-1">
        <p class="text-red-500 text-xs">(*) Thông tin bắt buộc</p>
    </div>
@endsection