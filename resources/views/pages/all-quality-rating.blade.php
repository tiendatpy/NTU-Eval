@extends('layouts.app')

@section('title', 'Danh sách đánh giá')

@section('content')
  <section class="mod-quality-rating bg-white rounded-2xl py-8">
    <div class="container">
      <div class="flex justify-between items-center mb-7">
        <h2 class="text-base mb-0">Danh Sách Xếp Loại Chất Lượng Của Đơn Vị</h2>
        <div class="flex items-center gap-3">
          <form id="yearFilterForm" action="{{ route('quality-ratings.list') }}" method="GET" class="flex items-center gap-3">
            <label for="year" class="font-medium">Năm:</label>
            <select id="year" name="year" class="border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
              @foreach($years as $year)
                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }} - {{ $year+1 }}</option>
              @endforeach
            </select>
          </form>
        </div>
      </div>

      <table class="w-full text-sm overflow-hidden">
        <thead class="bg-states-300">
          <tr>
            <th class="w-5p">STT</th>
            <th class="w-20p">Họ tên</th>
            <th class="w-15p">Mức xếp loại</th>
            <th class="w-60p">Diễn giải</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($evaluations as $index => $evaluation)
            <tr class="bg-white">
              <td>{{ $index + 1 }}</td>
              <td>{{ $evaluation->evaluator->full_name }}</td>
              <td>{{ $evaluation->quality->name }}</td>
              <td>
                <ul>
                  @foreach ($evaluation->details->pluck('evidence')->filter() as $evidence)
                    <p class="mb-3">{!! $evidence !!}</p>
                  @endforeach
                </ul>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">Không có đánh giá nào trong đơn vị.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

    </div>
  </section>
@endsection