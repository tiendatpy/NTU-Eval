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
          <th class="w-15p">Tên cá nhân</th>
          <th class="w-20p">Danh Hiệu</th>
          <th class="w-15p">Hình Thức <br> Khen Thưởng</th>
          <th class="w-25p">Tóm Tắt Thành Tích</th>
          <th class="w-10p">Trạng Thái</th>
          <th class="w-10p">Bình Xét</th>
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
              <button title="Thêm bình xét" class="text-xl px-2 text-states-600 hover:text-states-600/50 edit-btn" 
                      data-id="{{ $nomination->id }}" 
                      data-review="{{ $nomination->review }}">
                <span class="icomoon icon-pencil"></span>
              </button>
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
    <form action="{{ route('title-nominations.update', ':id') }}" method="POST" class="edit-review-form">
      @csrf
      @method('PUT')
      <div class="overlay js-popup fixed inset-0 bg-black/25 hidden">
        <!-- Popup -->
        <div class="review-popup absolute min-w-[400px] lg:w-[600px] transform-center-middle bg-white shadow-slate-600 rounded-lg p-4 z-50">
          <table class="w-full">
            <tr class="hover:bg-white border-b">
              <td class="font-semibold p-5 text-primary-800">Bình Xét</td>
              <td class="p-5 text-right text-2xl" >
                <button class="hover:text-states-500 close-popup"><span class="icomoon icon-close"></span></button>
              </td>
            </tr>
          </table>
          <div class="bg-white mt-5">
            <table class="mx-auto">
              <textarea class="ckeditor review-nomination" name="review" id="">
              </textarea>
            </table>
          </div>
            <div class="mt-4 text-right">
              <button type="submit" class="btn btn-primary">
                <span>Xác nhận</span>
              </button>
            </div>
          </div>
      </div>
    </form>
  </section>
@endsection