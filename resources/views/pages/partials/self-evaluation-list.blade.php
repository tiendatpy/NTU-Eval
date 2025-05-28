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
          <select id="year" name="year" class="border border-gray-300 rounded-md px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500" onchange="this.form.submit()">
            @foreach($years as $year)
              <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }} - {{ $year+1 }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </div>
    
    <!-- Hiển thị thống kê chỉ dành cho trưởng đơn vị -->
    @if($isUnitLeader && isset($statistics))
    <div class="mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-50 mr-4">
              <span class="icomoon icon-users flex text-states-500"></span>
            </div>
            <div>
              <p class="text-sm text-gray-500">Tổng số phiếu đánh giá</p>
              <p class="text-xl font-bold text-gray-700">{{ $statistics['total'] }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-50 mr-4">
              <span class="icomoon icon-check-circle text-green-500 flex"></span>
            </div>
            <div>
              <p class="text-sm text-gray-500">Đã phê duyệt</p>
              <p class="text-xl font-bold text-green-600">{{ $statistics['approved']['count'] }} <span class="text-sm font-normal text-gray-500">({{ $statistics['approved']['percentage'] }}%)</span></p>
            </div>
          </div>
        </div>

        <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-50 mr-4">
              <span class="icomoon icon-information-circle text-yellow-500 flex"></span>
            </div>
            <div>
              <p class="text-sm text-gray-500">Chờ phê duyệt</p>
              <p class="text-xl font-bold text-yellow-600">{{ $statistics['pending']['count'] }} <span class="text-sm font-normal text-gray-500">({{ $statistics['pending']['percentage'] }}%)</span></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Thanh tiến độ -->
      <div class="w-full bg-gray-200 rounded-full h-2.5 mt-4 mb-2">
        <div class="bg-neutral-500 h-2.5 rounded-full" style="width: {{ $statistics['approved']['percentage'] }}%"></div>
      </div>
      <p class="text-xs text-gray-500 text-right">{{ $statistics['approved']['percentage'] }}% đã được phê duyệt</p>
    </div>
    @endif
    
    <table class="w-full text-sm overflow-hidden">
      <thead class="bg-states-300">
        <tr>
          <th class="w-5p">STT</th>
          <th class="w-5p">Mã Phiếu</th>
          <th class="w-15p">Họ tên</th>
          <th class="w-15p">Xếp loại chất lượng</th>
          <th class="w-15p">Danh hiệu thi đua</th>
          <th class="w-15p">Người duyệt</th>
          <th class="w-15p">Trạng thái</th>
          <th class="w-10p">Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($evaluations as $index => $evaluation)
          <tr class="bg-white">
            <td>{{ $evaluations->firstItem() + $index }}</td>
            <td>{{ $evaluation->id }}</td>
            <td>{{ $evaluation->evaluator->full_name }}</td>
            <td>{{ $evaluation->status->name == 'Đã phê duyệt' ? $evaluation->approvedQuality->name :  $evaluation->quality->name  }}</td>
            <td>
              {{ $evaluation->status->name == 'Đã phê duyệt' ? $evaluation->approvedTitle->name : $evaluation->title->name}}
            </td>
            <td>{{ $evaluation->approver !== null ? $evaluation->approver->full_name : '' }}</td>
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
            <td colspan="8" class="text-center">Không có đánh giá nào trong đơn vị.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
    <div class="flex justify-end mt-5">
      {{ $evaluations->links() }}
    </div>
  </div>
</section>
@endsection
