@extends('layouts.app')

@section('title', 'Tự Đánh Giá')

@section('content')
  <section class="mod-self-eval bg-white rounded-2xl py-8">
    <div class="container">
      <div class="mb-8 text-sm">
        <h2 class="mb-7 text-base">Tự Đánh Giá</h2>
        <label for="period_id">Năm đánh giá</label>
        <select class="border-primary-300 border-1 px-5 py-4 w-auto rounded-lg period-after-evaluation" id="period_id" name="period_id" required>
          @foreach ($periods as $period)
            <option value="{{ $period->year }}"{{ $period->year == $selectedYear ? 'selected' : '' }}>
              {{ $period->year }} - {{ $period->year + 1 }}
            </option>
          @endforeach
        </select>
      </div>
      {{-- Hiển thị nội dung đánh giá hoặc form --}}
      <div id="evaluation-content">
        @include('pages.partials.evaluation-table', ['evaluation' => $evaluation, 'isOpenPeriod' => $isOpenPeriod])
      </div>
    </div>
  </section>
@endsection