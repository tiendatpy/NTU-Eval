@extends('layouts.app')
@section('title', 'Trang chủ')
@section('content')
@php
    $isAdmin = Auth::user()->role->isSuperAdmin;
@endphp
<div class="bg-white p-6 min-h-80p md:p-8 rounded-xl custom-box-shadow">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-800 mb-2">Xin chào, {{ Auth::user()->full_name }}!</h1>
        <p class="text-gray-600">Chào mừng bạn đến với Hệ thống Quản lý Đánh giá, Xếp loại của Trường Đại học Nha Trang</p>
    </div>

    @if($activePeriod && !$isAdmin)
        <div class="mb-8">
            <div class="relative overflow-hidden rounded-lg shadow-md">
                <div class="absolute top-0 left-0 w-1 h-full {{ $isOverdue ? 'bg-red-500' : ($daysRemaining <= 7 ? 'bg-orange-500' : 'bg-blue-500') }}"></div>
                <div class="p-6 {{ $isOverdue ? 'bg-red-50' : ($daysRemaining <= 7 ? 'bg-orange-50' : 'bg-blue-50') }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-base font-semibold mb-1 {{ $isOverdue ? 'text-red-700' : ($daysRemaining <= 7 ? 'text-orange-700' : 'text-blue-700') }}">
                                <span class="icomoon icon-calendar-check mr-2"></span>
                                {{ $activePeriod->name ?? ('Đợt đánh giá năm học ' . $activePeriod->year . ' - ' . ($activePeriod->year + 1)) }}
                            </h2>
                            <span class="text-sm {{ $isOverdue ? 'text-red-600' : ($daysRemaining <= 7 ? 'text-orange-600' : 'text-blue-600') }}">
                                Thời gian đánh giá: {{ format_date($activePeriod->start_date) }} - {{ format_date($activePeriod->end_date) }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            @if($isOverdue)
                                <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-bold text-white bg-red-500 rounded-full">
                                    Đã quá hạn
                                </span>
                            @elseif($daysRemaining <= 7)
                                <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-bold text-white bg-orange-500 rounded-full">
                                    Còn {{ $daysRemaining }} ngày
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-bold text-white bg-blue-500 rounded-full">
                                    Còn {{ $daysRemaining }} ngày
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
@endsection
