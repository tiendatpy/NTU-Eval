@extends('layouts.app')

@section('title', 'Trang Chủ')

@section('content')
    <div class="mod-test text-center bg-yellow-300 p-6 rounded-lg shadow-md" id="welcome-box">
        <h1 class="text-2xl font-bold">Chào mừng đến với Laravel!</h1>
        <p class="text-gray-600 mt-2">Đây là nội dung của trang chủ.</p>
        <button id="test-button" class="mt-4 px-4 py-2  text-white rounded">
            Click vào tôi
        </button>
    </div>
@endsection