@extends('layouts.app')

@section('content')
<section class="bg-white p-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-base mb-0">Quản lý danh hiệu thi đua</h2>
        <a href="{{ route('admin.titles.create') }}" class="btn btn-secondary__v2 flex items-center">
            <span class="icomoon icon-plus mr-2 text-xl flex"></span> Thêm mới
        </a>
    </div>

    <div class="mb-6">
        <form action="{{ route('admin.titles.index') }}" method="GET" class="flex flex-col down_lg:items-end lg:flex-wrap lg:flex-row items-center gap-4">
            <div class="flex-1 down_lg:w-full">
                <div class="header-search flex items-center">
                    <input name="search" value="{{ $search ?? '' }}" type="text" class="h-[45px] bg-primary-050 rounded-2xl border-1 w-full lg:w-[402px] px-7 py-6 text-sm" placeholder="Tìm kiếm theo tên danh hiệu...">
                </div>
            </div>
            <div class="w-auto down_lg:w-full">
                <select name="type" class="border rounded-md down_lg:w-full">
                    <option value="">-- Tất cả loại danh hiệu --</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ isset($typeFilter) && $typeFilter == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="wrap-button flex items-center gap-4">
                <button type="submit" class="btn btn-primary flex items-center">
                    <span class="icomoon icon-search mr-2 flex"></span> Tìm kiếm
                </button>
                <a href="{{ route('admin.titles.index') }}" class="btn btn-additional flex items-center">
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
                    <th>Tên danh hiệu</th>
                    <th>Loại danh hiệu</th>
                    <th>Mô tả</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($titles as $index => $title)
                <tr class="border-b">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $title->name }}</td>
                    <td>{{ $title->type->name ?? 'Chưa phân loại' }}</td>
                    <td>{{ Str::limit($title->description, 100) ?? 'Không có mô tả' }}</td>
                    <td class="flex nowrap justify-center items-center">
                        <a href="{{ route('admin.titles.show', $title) }}" class="flex text-blue-500 hover:text-blue-700 mx-1" title="Xem chi tiết">
                            <span class="flex icomoon icon-eye text-xl"></span>
                        </a>
                        <a href="{{ route('admin.titles.edit', $title) }}" class="flex text-yellow-500 hover:text-yellow-700 mx-1" title="Chỉnh sửa">
                            <span class="flex icomoon icon-pencil-alt text-xl"></span>
                        </a>
                        <form action="{{ route('admin.titles.destroy', $title) }}" method="POST" class="flex">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Xóa danh hiệu"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa danh hiệu thi đua này?')">
                                <span class="flex icomoon icon-trash-2 text-xl"></span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Không tìm thấy danh hiệu thi đua nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $titles->links() }}
    </div>
</section>
@endsection