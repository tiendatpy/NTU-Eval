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
          <select class="border-primary-500 border-1 p-2 w-100 rounded-lg" id="period" name="period_id" required>
            @foreach ($periods as $period)
            <option value="{{ $period->id }}"{{ $period->year == now()->year - 1 ? 'selected' : '' }}>
              {{ $period->year }} - {{ $period->year + 1 }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="self-table mb-5">
          <table class="w-full text-sm overflow-hidden" id="nominations-table">
            <thead class="rounded-t-xl">
              <tr class="bg-states-300">
                <th class="w-20p">Đề xuất danh hiệu</th>
                <th class="w-20p">Hình thức khen thưởng</th>
                <th class="w-50p">Tóm tắt thành tích</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <select name="title_id" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                    @foreach ($titles as $title)
                      <option value="{{ $title->id }}">{{ $title->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <select name="reward_id" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                    @foreach ($rewards as $reward)
                      <option value="{{ $reward->id }}">{{ $reward->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <textarea name="achievement" class="ckeditor border-primary-500 border-1 p-2 w-full rounded-lg" required></textarea>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="group-btn text-right mt-8">
          <button type="submit" class="btn btn-primary">Gửi</button>
        </div>
      </form>
    </div>
  </section>
@endsection