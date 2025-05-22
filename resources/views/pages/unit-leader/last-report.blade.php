@extends('layouts.app')

@section('title', 'Báo cáo tổng kết')

@section('content')
  <section class="mod-all-title-nominations bg-white">
    <div class="container py-8">
      <div class="flex justify-between items-center mb-7">
        <h2 class="text-base mb-0">Báo cáo tổng kết</h2>
        <div class="flex items-center gap-3">
          <form id="yearFilterForm" action="{{ route('title-nominations.list') }}" method="GET" class="flex items-center gap-3">
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
          <th class="w-15p">Tên cá nhân</th>
          <th class="w-20p">Danh Hiệu</th>
          <th class="w-60p">Trích ngang thành tích</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($evaluations as $index => $evaluation)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $evaluation->evaluator->full_name }}</td>
          <td>{{ $evaluation->title->name }}</td>
          <td>{{ $evaluation->reward->name }}</td>
          <td>{!! $evaluation->achievement !!}</td>
        </tr>
      @empty
      <tr>
        <td colspan="7" class="text-center py-4">Không có đề xuất khen thưởng nào trong đơn vị.</td>
      </tr>
      @endforelse
        </tbody>
      </table>
      <!-- Phân trang -->
      <div class="mt-4">
        {{ $evaluations->links() }}
      </div>
    </div>
  </section>
@endsection