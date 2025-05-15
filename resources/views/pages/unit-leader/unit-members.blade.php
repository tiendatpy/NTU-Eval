<!-- Trong resources/views/pages/unit-leader/unit-members.blade.php -->
@extends('layouts.app')

@section('title', 'Danh Sách Thành Viên Đơn Vị')

@section('content')
<section class="bg-white rounded-2xl py-8">
    <div class="container">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base mb-0">Danh Sách Thành Viên {{ auth()->user()->unit->name }}</h2>
            <span class="text-primary-700">Năm học: {{ $currentYear }} - {{ $currentYear + 1 }}</span>
        </div>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-states-300">
                    <th class=" text-left">STT</th>
                    <th class=" text-left">Mã cán bộ</th>
                    <th class=" text-left">Họ và tên</th>
                    <th class=" text-left">Email</th>
                    <th class=" text-left">Chức vụ</th>
                    <th class=" text-left">Số điện thoại</th>
                    <th class=" text-center">Trạng thái đánh giá</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                <tr class="border-b border-states-200 hover:bg-gray-50">
                    <td class="">{{ $index + 1 }}</td>
                    <td>{{ $member->id }}</td>
                    <td class="font-medium">{{ $member->full_name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->role->name }}</td>
                    <td>{{ $member->phone}}</td>
                    <td class=" text-center">
                        @if(in_array($member->id, $evaluatedUserIds ?? []))
                            <span class="inline-flex items-center px-3 py-2 rounded-full font-medium bg-green-100 text-neutral-600">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Đã đánh giá
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full font-medium bg-red-100 text-secondary-600">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Chưa đánh giá
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class=" text-center">Không có thành viên nào trong đơn vị.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $members->links() }}
        </div>
    </div>
</section>
@endsection