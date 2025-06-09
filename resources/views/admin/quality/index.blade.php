@extends('layouts.app')

@section('content')
<section class="bg-white p-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-base mb-0">Quản lý xếp loại chất lượng</h2>
        <a href="{{ route('admin.quality.create') }}" class="btn btn-secondary__v2 flex items-center">
            <span class="icomoon icon-plus mr-2 text-xl flex"></span> Thêm mới
        </a>
    </div>

    <div class="mb-6">
        <form action="{{ route('admin.quality.index') }}" method="GET" class="flex flex-col down_lg:items-end lg:flex-wrap lg:flex-row items-center gap-4">
            <div class="flex-1 down_lg:w-full">
                <div class="header-search flex items-center">
                    <input name="search" value="{{ $search ?? '' }}" type="text" class="h-[45px] bg-primary-050 rounded-2xl border-1 w-full lg:w-[402px] px-7 py-6 text-sm" placeholder="Tìm kiếm theo tên xếp loại...">
                </div>
            </div>
            <div class="wrap-button flex items-center gap-4">
                <button type="submit" class="btn btn-primary flex items-center">
                    <span class="icomoon icon-search mr-2 flex"></span> Tìm kiếm
                </button>
                <a href="{{ route('admin.quality.index') }}" class="btn btn-additional flex items-center">
                    <span class="icomoon icon-refresh mr-2 flex"></span> Đặt lại
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-states-300">
                <tr>
                    <th>STT</th>
                    <th>ID</th>
                    <th>Tên xếp loại</th>
                    <th>Mô tả</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($qualities as $index => $quality)
                <tr class="border-b">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $quality->id }}</td>
                    <td>{{ $quality->name }}</td>
                    <td>{{ Str::limit($quality->description, 100) ?? 'Không có mô tả' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.quality.show', $quality) }}" class="text-blue-500 hover:text-blue-700 mx-1" title="Xem chi tiết">
                            <span class="icomoon icon-eye text-xl"></span>
                        </a>
                        <a href="{{ route('admin.quality.edit', $quality) }}" class="text-yellow-500 hover:text-yellow-700 mx-1" title="Chỉnh sửa">
                            <span class="icomoon icon-pencil-alt text-xl"></span>
                        </a>
                        <form action="{{ route('admin.quality.destroy', $quality) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 mx-1" title="Xóa xếp loại"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa xếp loại chất lượng này?')">
                                <span class="icomoon icon-trash-2 text-xl"></span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Không tìm thấy xếp loại chất lượng nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $qualities->links() }}
    </div>
</section>
@endsection