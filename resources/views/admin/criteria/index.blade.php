@extends('layouts.app')

@section('content')
<section class="bg-white p-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-base mb-0">Quản lý tiêu chí đánh giá</h2>
        <a href="{{ route('admin.criteria.create') }}" class="btn btn-secondary__v2 flex items-center">
            <span class="icomoon icon-plus mr-2 text-xl flex"></span> Thêm mới
        </a>
    </div>

    <div class="mb-6">
        <form action="{{ route('admin.criteria.index') }}" method="GET" class="flex flex-col down_lg:items-end lg:flex-wrap lg:flex-row items-center gap-4">
            <div class="flex-1 down_lg:w-full">
                <div class="header-search flex items-center">
                    <input name="search" value="{{ $search ?? '' }}" type="text" class="h-[45px] bg-primary-050 rounded-2xl border-1 w-full lg:w-[402px] px-7 py-6 text-sm" placeholder="Tìm kiếm theo tên tiêu chí...">
                </div>
            </div>
            <div class="w-auto down_lg:w-full">
                <select name="category" class="border rounded-md down_lg:w-full h-[45px]">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ isset($categoryFilter) && $categoryFilter == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="wrap-button flex items-center gap-4">
                <button type="submit" class="btn btn-primary flex items-center">
                    <span class="icomoon icon-search mr-2 flex"></span> Tìm kiếm
                </button>
                <a href="{{ route('admin.criteria.index') }}" class="btn btn-additional flex items-center">
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
                    <th>Tên tiêu chí</th>
                    <th>Danh mục</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($criteria as $index => $criterion)
                <tr class="border-b">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $criterion->name }}</td>
                    <td>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {{ $criterion->category->name ?? 'Chưa phân loại' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.criteria.show', $criterion) }}" class="text-blue-500 hover:text-blue-700 mx-1" title="Xem chi tiết">
                            <span class="icomoon icon-eye text-xl"></span>
                        </a>
                        <a href="{{ route('admin.criteria.edit', $criterion) }}" class="text-yellow-500 hover:text-yellow-700 mx-1" title="Chỉnh sửa">
                            <span class="icomoon icon-pencil-alt text-xl"></span>
                        </a>
                        <form action="{{ route('admin.criteria.destroy', $criterion) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Xóa tiêu chí"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa tiêu chí đánh giá này?')">
                                <span class="icomoon icon-trash-2 text-xl"></span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Không tìm thấy tiêu chí đánh giá nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-end">
        {{ $criteria->links() }}
    </div>
</section>
@endsection