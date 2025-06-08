<!-- Template cho trang show -->
@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">{{ $title ?? 'Chi tiết' }}</h2>
        <div>
            <a href="{{ $editRoute }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-2">
                <span class="icomoon icon-pencil mr-1"></span> Chỉnh sửa
            </a>
            <a href="{{ $backRoute }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <span class="icomoon icon-arrow-left mr-1"></span> Quay lại
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-lg overflow-hidden border">
        <!-- Detail content -->
        @yield('detail-content')
    </div>
@endsection