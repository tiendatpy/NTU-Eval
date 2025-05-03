@extends('layouts.app')

@section('title', 'Danh Sách Đề Xuất Khen Thưởng')

@section('content')
  <section class="all-title-nominations bg-white">
    <div class="container py-8">
      <h2 class="mb-7 text-base">Danh Sách Đề Xuất Khen Thưởng</h2>
      <table class="w-full text-sm overflow-hidden">
        <thead class="bg-states-300">
        <tr>
          <th class="w-5p">STT</th>
          <th class="w-15p">Tên các nhân</th>
          <th class="w-20p">Danh Hiệu</th>
          <th class="w-20p">Hình Thức Khen Thưởng</th>
          <th class="w-25p">Tóm Tắt Thành Tích</th>
          <th class="w-10p">Trạng Thái</th>
          <th class="w-5p">Bình Xét</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($nominations as $index => $nomination)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $nomination->user->full_name }}</td>
          <td>{{ $nomination->title->name }}</td>
          <td>{{ $nomination->reward->name }}</td>
          <td>{{ $nomination->achievement }}</td>
          <td>{{ $nomination->status->name }}</td>
          <td>
            <div class="flex justify-center items-center gap-2">
              <button class="text-xl text-states-500" ><span class="icomoon icon-eye"></span></button>
              <button class="text-xl text-secondary-500" ><span class="icomoon icon-pencil"></span></button>
            </div>
          </td>
        </tr>
      @endforeach
        </tbody>
      </table>

      <!-- Phân trang -->
      <div class="mt-4">
        {{ $nominations->links() }}
      </div>
    </div>
  </section>
@endsection