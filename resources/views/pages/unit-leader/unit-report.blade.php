@extends('layouts.app')

@section('title', 'Danh sách đánh giá')

@section('content')
  <section class="mod-unit-report bg-white rounded-2xl py-8">
    <div class="container">
      <div class="flex justify-between items-center mb-7">
        <h2 class="text-base mb-0">Danh Sách Tự Đánh Giá Của Đơn Vị</h2>
        <div class="flex items-center gap-3">
          <form method="get" action="{{ route('unit-report') }}" class="flex items-center gap-3">
            <label for="year" class="text-sm font-medium text-gray-700">Năm học:</label>
            <select id="year" name="year" 
              class="border border-gray-300 text-gray-700 rounded-lg px-4 py-2.5 focus:ring-blue-500 focus:border-blue-500" 
              onchange="this.form.submit()">
              @foreach($years as $y)
                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }} - {{ $y + 1 }}</option>
              @endforeach
            </select>
          </form>
          <a href="{{ route('evaluations.export-unit-report') }}?year={{ $selectedYear }}" class="btn btn-primary inline-flex items-center gap-2">
            <span class="icomoon icon-download-v2"></span>
            <span>Xuất báo cáo</span>
          </a>
        </div>
      </div>

      <table class="w-full text-sm overflow-hidden">
        <thead class="bg-states-300">
          <tr>
            <th class="w-5p">STT</th>
            <th class="w-20p">Họ tên</th>
            <th class="w-15p">Mức xếp loại; DHTĐ</th>
            <th class="w-60p">Diễn giải</th>
          </tr>
        </thead>
        <tbody>
          @if(!$evaluations->isEmpty())
            <tr>
              <td><strong>A</strong></td>
              <td><strong>Cá nhân</strong></td>
              <td colspan="3"></td>
            </tr>
            @foreach ($evaluations as $index => $evaluation)
              <tr class="bg-white">
                <td>{{ $index + 1 }}</td>
                <td>{{ $evaluation->evaluator->full_name }}</td>
                <td>
                  <p>{{ $evaluation->quality->name }}</p>
                  <p>{{ $evaluation->title->name }}</p>
                </td>
                <td>
                  {!! $evaluation->achievement !!}
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="4" class="text-center">Không có đánh giá nào trong đơn vị.</td>
            </tr>
          @endif

          @if($unitEvaluation)
          <tr>
            <td><strong>B</strong></td>
            <td><strong>Tập thể đơn vị</strong></td>
            <td>
              <p>{{ $unitEvaluation->quality->name }}</p>
              <p>{{ $unitEvaluation->title->name }}</p>
            </td>
            <td>{!! $unitEvaluation->evidence !!}</td>
          </tr>
          @endif  
        </tbody>
      </table>
      <div class="flex justify-end">
        {{ $evaluations->links() }}
      </div>
    </div>
  </section>
@endsection