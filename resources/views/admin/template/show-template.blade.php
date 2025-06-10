<!-- Template cho trang show -->
@extends('layouts.app')

@section('content')
    <section class="bg-white py-8">
        <div class="container">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base mb-0">Chi tiết</h2>
                <div>
                    <a href="{{ $editRoute }}" class="btn btn-secondary__v2">
                       Chỉnh sửa
                    </a>
                    <a href="{{ $backRoute }}" class="btn btn-additional">
                        Quay lại
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-lg overflow-hidden border">
                @yield('detail-content')
            </div>
        </div>
    </section>
@endsection