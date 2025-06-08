<!-- Template cho trang edit -->
@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-bold">{{ $title ?? 'Chỉnh sửa' }}</h2>
        <p class="text-gray-600">{{ $description ?? 'Vui lòng cập nhật thông tin bên dưới' }}</p>
    </div>

    <form action="{{ $route }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <!-- Form fields -->
        @yield('form-fields')
        
        <div class="flex justify-end space-x-3">
            <a href="{{ $cancelRoute }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-states-500 text-white rounded hover:bg-states-600">
                Cập nhật
            </button>
        </div>
    </form>
@endsection