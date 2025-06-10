@extends('admin.template.create-template')

@section('title', 'Thêm mới hình thức khen thưởng')

@php
$route = route('admin.rewards.store');
$cancelRoute = route('admin.rewards.index');
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
    
    <div class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên hình thức khen thưởng <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <textarea name="description" id="description" rows="4"
                class="w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">{{ old('description') }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Mô tả chi tiết về hình thức khen thưởng này</p>
        </div>
    </div>
    
    <div class="mt-4 pl-1">
        <p class="text-red-500 text-xs">(*) Thông tin bắt buộc</p>
    </div>
@endsection