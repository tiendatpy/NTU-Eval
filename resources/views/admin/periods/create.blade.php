@extends('admin.template.create-template')

@section('title', 'Thêm mới đợt đánh giá')

@php
$route = route('admin.periods.store');
$cancelRoute = route('admin.periods.index');
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
            <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Năm học <span class="text-red-500">*</span></label>
            <input type="number" name="year" id="year" value="{{ old('year', date('Y')) }}" required min="2000" max="2100"
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
            <p class="text-xs text-gray-500 mt-1">Nhập năm bắt đầu của năm học (VD: 2025 cho năm học 2025-2026)</p>
        </div>
        
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu <span class="text-red-500">*</span></label>
            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
        </div>
        
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc <span class="text-red-500">*</span></label>
            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
        </div>
        
        <div>
            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
            <select name="status_id" id="status_id" required
                class="w-full h-[46px] rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
                <option value="">-- Chọn trạng thái --</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="mt-4 pl-1">
        <p class="text-red-500 text-xs">(*) Thông tin bắt buộc</p>
    </div>
@endsection