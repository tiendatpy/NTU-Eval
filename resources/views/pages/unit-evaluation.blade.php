@extends('layouts.app')

@section('title', 'Tự Đánh Giá Đơn Vị')

@section('content')
<section class="mod-unit-eval bg-white rounded-2xl py-8">
  <div class="container">
    <div class="mb-8 text-sm">
      <h2 class="mb-7 text-base">Tự Đánh Giá Đơn Vị</h2>
      <label for="period_id">Năm đánh giá</label>
      <select class="border-primary-500 border-1 p-2 w-100 rounded-lg period-unit-evaluation" id="period_id" name="period_id" required>
        @foreach ($periods as $period)
          <option value="{{ $period->year }}"{{ $period->year == $selectedYear ? 'selected' : '' }}>
            {{ $period->year }} - {{ $period->year + 1 }}
          </option>
        @endforeach
      </select>
    </div>
    
    <div id="unit-evaluation-content">
      @include('pages.partials.unit-evaluation-form', [
        'unitEvaluation' => $unitEvaluation, 
        'isCurrentYear' => $isCurrentYear,
        'quality' => $quality,
        'titles' => $titles,
        'rewards' => $rewards
      ])
    </div>
  </div>
</section>
@endsection