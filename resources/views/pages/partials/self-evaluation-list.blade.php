@extends('layouts.app')

@section('title', 'Danh sách đánh giá')

@section('content')
<section class="mod-self-eval-list bg-white rounded-2xl py-8">
  <div class="container">
    <div class="flex justify-between items-center mb-7">
      <h2 class="text-base mb-0">Danh Sách Các Phiếu Đánh Giá</h2>
      <div class="flex items-center gap-3">
        <form id="yearFilterForm" action="{{ route('evaluations.list') }}" method="GET" class="flex items-center gap-3">
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
          <th class="w-10p">Mã Phiếu</th>
          <th class="w-15p">Họ tên</th>
          <th class="w-20p">Xếp loại chất lượng</th>
          <th class="w-15p">Danh hiệu thi đua</th>
          <th class="w-10p">Trạng thái</th>
          <th class="w-10p">Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($evaluations as $index => $evaluation)
          <tr class="bg-white">
            <td>{{ $index + 1 }}</td>
            <td>{{ $evaluation->id }}</td>
            <td>{{ $evaluation->evaluator->full_name }}</td>
            <td>{{ $evaluation->quality->name }}</td>
            <td>
              {{ $evaluation->title->name}}
            </td>
            <td>
              <span class="flex items-center gap-4">
                <span class="min-w-5 h-5 rounded-full 
                    @if ($evaluation->status->name == 'Đang xét duyệt') bg-secondary-600
                    @elseif ($evaluation->status->name == 'Đã phê duyệt') bg-neutral-500
                    @endif
                "></span>
                <span>{{ $evaluation->status->name }}</span>
              </span>
            </td>
            <td>
              <a href="{{ route('all-evaluations.view-details', $evaluation->id) }}" class="btn btn-secondary inline-flex items-center gap-3">
                <span class="icomoon icon-eye text-xl"></span>
                <span>Xem</span>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">Không có đánh giá nào trong đơn vị.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
    @if ($isUnitLeader)
      <div class="text-right mt-5">
        <a href="{{ route('evaluations.export-quality-ratings', $evaluations->first()->id) }}" class="btn btn-primary inline-flex items-center gap-3" title="Xuất danh sách tự đánh giá">
          <span class="icomoon icon-download-v2 text-xl"></span>
          <span>Xuất File</span>
        </a>
      </div>
    @endif
  </div>
</section>
@endsection