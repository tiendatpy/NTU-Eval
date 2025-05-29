@extends('layouts.app')

@section('title', 'Chỉnh Sửa Đợt Đánh Giá')

@section('content')
<section class="mod-period-edit bg-white rounded-2xl custom-box-shadow py-8">
  <div class="container max-w-4xl mx-auto px-4">    
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h2 class="text-base mb-3">Chỉnh Sửa Đợt Đánh Giá</h2>
        <p class="text-primary-700 font-medium">{{ auth()->user()->unit->name }}</p>
      </div>
    </div>

    
    <div class="bg-white rounded-xl custom-box-shadow overflow-hidden mb-8 border border-gray-200">
      <div class="bg-gradient-to-r from-states-500 to-states-800 px-6 py-8 text-center">
        <h3 class="text-white mb-0">Thông tin đợt đánh giá</h3>
      </div>
      <div class="p-6">
        <form action="{{ route('periods.update', $period->id) }}" method="POST">
          @csrf
          @method('PUT')            
          <div class="mb-6">
              <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Năm đánh giá <span class="text-red-500">*</span></label>
              <div class="mt-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="icomoon icon-calendar text-gray-500"></span>
                </div>
                <select id="year" name="year" 
                       class="pl-10 w-full @error('year') border-red-300 @enderror  transition duration-150 ease-in-out" 
                       required>
                  <option value="">-- Chọn năm học --</option>
                  @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ old('year', $period->year) == $y ? 'selected' : '' }}>
                      {{ $y }}
                    </option>
                  @endfor
                </select>
              </div>
              @error('year')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @else
                <div class="mt-1 flex items-center text-xs text-gray-500">
                  <span class="icomoon icon-info mr-1"></span>
                  <span>Năm học của đợt đánh giá (mỗi năm chỉ được mở 1 đợt đánh giá)</span>
                </div>
              @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="relative">
              <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu <span class="text-red-500">*</span></label>
              <div class="mt-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="icomoon icon-calendar text-blue-500"></span>
                </div>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $period->start_date->format('Y-m-d')) }}" 
                       class="border-primary-300 border-1 pl-12 pr-5 py-4 rounded-lg w-full @error('start_date') border-red-300 @enderror  hover:border-blue-300 transition duration-150 ease-in-out"
                       required>
              </div>
              @error('start_date')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                  <span class="icomoon icon-alert-circle mr-1"></span>
                  {{ $message }}
                </p>
              @else
                <div class="mt-1 flex items-center text-xs text-gray-500">
                  <span class="icomoon icon-info mr-1"></span>
                  <span>Ngày bắt đầu của đợt đánh giá</span>
                </div>
              @enderror
            </div>
            
            <div class="relative">
              <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc <span class="text-red-500">*</span></label>
              <div class="mt-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="icomoon icon-calendar text-blue-500"></span>
                </div>
                <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $period->end_date->format('Y-m-d')) }}" 
                       class="border-primary-300 border-1 pl-12 pr-5 py-4 rounded-lg w-full @error('end_date') border-red-300 @enderror  hover:border-blue-300 transition duration-150 ease-in-out"
                       required>
              </div>
              @error('end_date')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                  <span class="icomoon icon-alert-circle mr-1"></span>
                  {{ $message }}
                </p>
              @else
                <div class="mt-1 flex items-center text-xs text-gray-500">
                  <span class="icomoon icon-info mr-1"></span>
                  <span>Ngày kết thúc của đợt đánh giá</span>
                </div>
              @enderror
            </div>
          </div>
            <div class="mb-8">
            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
            <div class="mt-1 relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="icomoon icon-check-circle text-blue-500"></span>
              </div>
              <select id="status_id" name="status_id" 
                     class="pl-10 w-full  @error('status_id') border-red-300 @enderror  hover:border-blue-300 transition duration-150 ease-in-out" 
                     required>
                <option value="">-- Chọn trạng thái --</option>
                @foreach($statuses as $status)
                  <option value="{{ $status->id }}" 
                          {{ old('status_id', $period->status_id) == $status->id ? 'selected' : '' }}
                          class="{{ $status->name == 'Mở' ? 'text-green-600 font-medium' : ($status->name == 'Đóng' ? 'text-red-600 font-medium' : 'text-yellow-600 font-medium') }}">
                    {{ $status->name }}
                  </option>
                @endforeach
              </select>
            </div>
            @error('status_id')
              <p class="mt-1 text-sm text-red-600 flex items-center">
                <span class="icomoon icon-alert-circle mr-1"></span>
                {{ $message }}
              </p>
            @else
              <div class="mt-3 bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-md text-sm">
                <h4 class="font-medium mb-2 flex items-center">
                  <span class="icomoon icon-info-circle mr-1.5"></span>
                  Ý nghĩa trạng thái
                </h4>
                <ul class="list-disc pl-5 space-y-1.5">
                  <li><strong class="text-green-600">Mở:</strong> Thành viên có thể thực hiện đánh giá</li>
                  <li><strong class="text-red-600">Đóng:</strong> Đợt đánh giá kết thúc, không thể chỉnh sửa hoặc thêm đánh giá mới</li>
                </ul>
              </div>
            @enderror
          </div>          <div class="flex justify-end border-t border-gray-200 pt-6">
            <div class="flex space-x-3">
              <a href="{{ route('periods.index') }}" class="btn btn-additional">
                <span class="icomoon icon-x mr-1.5"></span>
                Quay lại
              </a>
              <button type="submit" class="btn btn-primary">
                <span class="icomoon icon-save mr-1.5"></span>
                Lưu thay đổi
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-5 rounded-lg mb-6  hover:shadow transition-shadow duration-300">
      <div class="flex items-start">
        <div class="flex-shrink-0 bg-yellow-100 p-1.5 rounded-full">
          <span class="icomoon icon-info text-yellow-500 text-lg"></span>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-semibold text-yellow-800">Lưu ý quan trọng</h3>
          <div class="mt-2 text-sm text-yellow-700">
            <p>Khi đóng đợt đánh giá, tất cả người dùng sẽ không thể thực hiện hoặc chỉnh sửa đánh giá. Hãy đảm bảo thông báo trước cho các thành viên.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection