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

      <form action="{{ route('evaluations.approve-quality') }}" method="POST" id="approvalForm">
        @csrf
        
        <table class="w-full text-sm overflow-hidden">
          <thead class="bg-states-300">
            <tr>
              <th class="w-5p">STT</th>
              <th class="w-15p">Họ tên</th>
              <th class="w-10p">Mức xếp loại</th>
              <th class="w-50p">Diễn giải</th>
              <th class="w-20p">Duyệt</th>
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
                      <p class="mb-3">- {{ $evidence }}</p>
                    @endforeach
                  </ul>
                </td>
                <td>
                  <input type="hidden" name="evaluations[{{ $index }}][id]" value="{{ $evaluation->id }}">
                  <select name="evaluations[{{ $index }}][quality_id]" class="border-primary-300 border-1 p-2 w-full rounded-lg approve-quality">
                    @foreach ($quality as $item)
                      <option value="{{ $item->id }}" {{ ($evaluation->approved_quality_id ? $evaluation->approved_quality_id : $evaluation->quality_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                      </option>
                    @endforeach
                  </select>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">Không có đánh giá nào trong đơn vị.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
        @if ($evaluations->count() > 0)
          @php
          $allApproved = $evaluations->filter(function($evaluation) {
              return $evaluation->evaluator->role->name !== 'Trưởng đơn vị';
          })->every(function($evaluation) {
              return !is_null($evaluation->approved_quality_id);
          });
          @endphp
          <div class="text-right mt-5">
            <a href="{{ route('evaluations.export', $evaluations->first()->id) }}" class="btn btn-secondary inline-flex items-center gap-3">
              <span class="icomoon icon-download-v2 text-xl"></span>
              <span>Xuất File</span>
            </a>
            <button type="submit" class="btn btn-primary {{ $allApproved ? 'bg-states-600/80 cursor-not-allowed' : '' }}" 
                    {{ $allApproved ? 'disabled' : '' }}>
              <span>{{ $allApproved ? 'Đã phê duyệt' : 'Xác nhận' }}</span>
            </button>
          </div>
        @endif
      </form>
    </div>
  </section>
@endsection