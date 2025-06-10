@extends('layouts.app')

@section('content')
<section class="bg-white p-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-base mb-0">Quản lý đợt đánh giá</h2>
        <a href="{{ route('admin.periods.create') }}" class="btn btn-secondary__v2 flex items-center">
            <span class="icomoon icon-plus mr-2 text-xl flex"></span> Thêm mới
        </a>
    </div>

    <div class="mb-6">
        <form action="{{ route('admin.periods.index') }}" method="GET" class="flex flex-col down_lg:items-end lg:flex-wrap lg:flex-row lg:justify-end gap-4">
            <div class="w-auto down_lg:w-full">
                <select name="year" class="border rounded-md down_lg:w-full h-[45px]">
                    <option value="">-- Tất cả năm học --</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ isset($yearFilter) && $yearFilter == $year ? 'selected' : '' }}>
                            {{ $year }} - {{ $year + 1 }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-auto down_lg:w-full">
                <select name="status" class="border rounded-md down_lg:w-full h-[45px]">
                    <option value="">-- Tất cả trạng thái --</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ isset($statusFilter) && $statusFilter == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="wrap-button flex items-center gap-4">
                <button type="submit" class="btn btn-primary flex items-center">
                    <span class="icomoon icon-search mr-2 flex"></span> Tìm kiếm
                </button>
                <a href="{{ route('admin.periods.index') }}" class="btn btn-additional flex items-center">
                    <span class="icomoon icon-refresh mr-2 flex"></span> Đặt lại
                </a>
            </div>
        </form>
    </div>


    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-states-300">
                <tr>
                    <th>STT</th>
                    <th>Năm học</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periods as $index => $period)
                <tr class="border-b">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $period->year }} - {{ $period->year + 1 }}</td>
                    <td>
                        <span class="text-sm">
                            {{ format_date($period->start_date, 'd/m/Y') }} - {{ format_date($period->end_date, 'd/m/Y') }}
                        </span>
                    </td>
                    <td>
                        <span class="px-2 py-1 rounded-full 
                            @if($period->status->name == 'Mở') bg-green-100 text-green-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $period->status->name }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.periods.show', $period) }}" class="text-blue-500 hover:text-blue-700 mx-1" title="Xem chi tiết">
                            <span class="icomoon icon-eye text-xl"></span>
                        </a>
                        <a href="{{ route('admin.periods.edit', $period) }}" class="text-yellow-500 hover:text-yellow-700 mx-1" title="Chỉnh sửa">
                            <span class="icomoon icon-pencil-alt text-xl"></span>
                        </a>
                        <form action="{{ route('admin.periods.destroy', $period) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Xóa đợt đánh giá"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa đợt đánh giá này?')">
                                <span class="icomoon icon-trash-2 text-xl"></span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Không tìm thấy đợt đánh giá nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $periods->links() }}
    </div>
</section>
@endsection