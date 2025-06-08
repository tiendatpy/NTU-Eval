@extends('layouts.app')

@section('content')
    <section class="bg-white py-8">
        <div class="container max-w-4xl mx-auto">
            <div class="mb-6">
                <h2 class="text-base mb-0">{{ $title ?? 'Thêm mới' }}</h2>
            </div>
            <form action="{{ $route }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Form fields -->
                @yield('form-fields')
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ $cancelRoute }}" class="btn btn-tertiary">
                        Hủy
                    </a>
                    <button type="submit" class="btn btn-secondary__v2">
                        Lưu
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection