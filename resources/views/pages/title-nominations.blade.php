@extends('layouts.app')

@section('title', 'Đề Xuất Danh Hiệu Thi Đua')

@section('content')
  <section class="mod-title-nomination bg-white">
    <div class="container py-8">
      <form action="{{ route('title-nominations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="title-nomination-heading mb-8 text-sm">
          <h2 class="mb-7 text-base">Đề xuất danh hiệu</h2>
          <label for="period">Năm học</label>
          <select class="border-primary-500 border-1 p-2 w-100 rounded-lg" id="period" name="period" required>
            @for ($year = now()->year-3; $year <= now()->year; $year++)
              <option value="{{ $year }}">{{ $year }}-{{ $year + 1 }}</option>
            @endfor
          </select>
        </div>
        <div class="self-table mb-5">
          <table class="w-full text-sm overflow-hidden" id="nominations-table">
            <thead class="rounded-t-xl">
              <tr class="bg-states-300">
                <th class="w-5p">STT</th>
                <th class="w-20p">Đề xuất danh hiệu</th>
                <th class="w-20p">Hình thức khen thưởng</th>
                <th class="w-50p">Tóm tắt thành tích</th>
                <th class="w-10p">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>
                  <select name="titles[]" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                    @foreach ($titles as $title)
                      <option value="{{ $title->id }}">{{ $title->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <select name="rewards[]" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                    @foreach ($rewards as $reward)
                      <option value="{{ $reward->id }}">{{ $reward->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <textarea name="achievements[]" class="ckeditor border-primary-500 border-1 p-2 w-full rounded-lg" required></textarea>
                </td>
                <td>
                  <button type="button" class="btn btn-tertiary remove-row">Xóa</button>
                </td>
              </tr>
            </tbody>
          </table>
          <button type="button" class="btn btn-secondary mt-4" id="add-row">+ Thêm hàng</button>
        </div>
        <div class="group-btn text-right mt-8">
          <button type="submit" class="btn btn-primary">Gửi</button>
        </div>
      </form>
    </div>
  </section>
@endsection