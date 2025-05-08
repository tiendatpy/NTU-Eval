@extends('layouts.app')

@section('title', 'Chi Tiết Xét Duyệt Danh Hiệu Thi Đua')

@section('content')
  <section class="unit-leader-approval-detail bg-white rounded-2xl py-8">
    <div class="container">
      <h2 class="mb-7">Chi Tiết Xét Duyệt</h2>
      <fieldset class="border-1 p-5 rounded-lg">
        <legend><i>Tự đánh giá</i></legend>
        <div>
          <div class="mb-6">
            <p><strong>Tên cá nhân:</strong> {{ $nomination->user->full_name }}</p>
            <p><strong>Danh hiệu:</strong> {{ $nomination->title->name }}</p>
            <p><strong>Hình thức khen thưởng:</strong> {{ $nomination->reward->name }}</p>
            <p><strong>Tóm tắt thành tích:</strong> {{ $nomination->achievement }}</p>
          </div>
          <div class="mb-4">
            <label for="review" class="block font-bold mb-4">Bình xét:</label>
            <textarea class="ckeditor border-primary-500 border-1 p-2 w-full rounded-lg" name="review" id="review" rows="5">{{ $nomination->review }}</textarea>
          </div>
        </div>
      </fieldset>
      <form action="{{ route('unit-leader-approvals.approve', $nomination->id) }}" method="POST">
        @csrf
        @method('PUT')
        <fieldset class="border-1 p-5 rounded-lg mt-5" >
          <legend><i>Xét duyệt</i></legend>
          <div class="mb-4">
            <label for="approved_title_id" class="block font-bold mb-4">Danh hiệu được duyệt:</label>
            <select name="approved_title_id" id="approved_title_id" class="border-primary-500 border-1 p-2 w-auto rounded-lg">
              @foreach ($titles as $title)
                <option value="{{ $title->id }}" {{ $nomination->title_id == $title->id ? 'selected' : '' }}>
                  {{ $title->name }}
                </option>
              @endforeach
            </select>
          </div>
        </fieldset>
        <div class="text-right mt-5">
          <button type="submit" class="btn btn-primary">Xác nhận</button>
          <button type="button" class="btn btn-secondary">
            <a href="{{route('title-nominations.list')}}">Quay lại</a>
          </button>
        </div>
      </form>
    </div>
  </section>
@endsection