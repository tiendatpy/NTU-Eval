<!-- Template cho trang edit -->
@extends('layouts.app')

@section('content')
    <section class="bg-white py-8">
        <div class="container">
            <div class="mb-6">
                <h2 class="text-base mb-0">Chỉnh sửa</h2>
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
                    <a href="{{ $cancelRoute }}" class="btn btn-tertiary">
                        Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Cập nhật
                    </button>
                </div>
            </form>
        
        </div>
    </section>
@endsection