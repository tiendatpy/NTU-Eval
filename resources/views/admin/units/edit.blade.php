@extends('admin.template.edit-template')

@section('title', 'Chỉnh sửa đơn vị')

@php
$route = route('admin.units.update', $unit);
$cancelRoute = route('admin.units.index');
@endphp

@section('form-fields')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div >
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Tên đơn vị <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}" required
                class="w-full h-17 rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
        </div>
        
        <div>
            <label for="type_id" class="block text-sm font-medium text-gray-700 mb-1">Loại đơn vị <span class="text-red-500">*</span></label>
            <select name="type_id" id="type_id" required
                class="h-17 w-full rounded-2xl border border-gray-300 bg-primary-050 px-4 py-3">
                <option value="">-- Chọn loại đơn vị --</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('type_id', $unit->type_id) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>
    
    <div class="mt-4 pl-1">
        <p class="text-red-500 text-xs">(*) Thông tin bắt buộc</p>
    </div>
@endsection