@extends('layouts.app')

@section('title', 'Đề Xuất Danh Hiệu Thi Đua')

@section('content')
  <section class="mod-title-nomination bg-white">
    <div class="container py-8">
      {{-- Dropdown chọn năm --}}
      <div class="title-nomination-heading mb-8 text-sm">
        <h2 class="mb-7 text-base">Đề Xuất Danh Hiệu Thi Đua</h2>
        <label for="period">Năm học</label>
        <select class="border-primary-500 border-1 p-2 w-100 rounded-lg period-after-nomination" id="period" name="period_id" required>
          @foreach ($periods as $period)
            <option value="{{ $period->year }}"{{ $period->year == now()->year - 1 ? 'selected' : '' }}>
              {{ $period->year }} - {{ $period->year + 1 }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Nội dung đề xuất danh hiệu --}}
      <div id="nomination-content">
        @include('pages.partials.nomination-table', ['titles' => $titles, 'rewards' => $rewards, 'nominations' => $nominations, 'isCurrentYear' => $isCurrentYear])
      </div>
    </div>
  </section>
@endsection