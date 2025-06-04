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
    
    <!-- Form tìm kiếm và lọc -->
    <div class="mb-6 ">
      <form action="{{ route('evaluations.list') }}" method="GET" class="flex flex-col gap-4">
        <!-- Giữ lại năm đã chọn -->
        <input type="hidden" name="year" value="{{ $selectedYear }}">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 lg:items-center gap-4">
          <!-- Tìm kiếm theo tên -->
          <div>
            <div class="header-search flex items-center">
                <input name="search_name" value="{{ $searchName ?? '' }}" type="text" class=" bg-primary-050 rounded-2xl border-1 w-full px-7 py-5 text-sm" placeholder="Tìm theo tên cán bộ...">
            </div>
          </div>
          
          <!-- Lọc theo xếp loại chất lượng -->
          <div>
            <select id="quality_id" name="quality_id" 
              class="w-full">
              <option value="">-- Tất cả xếp loại --</option>
              @foreach($qualities as $quality)
                <option value="{{ $quality->id }}" {{ isset($qualityId) && $qualityId == $quality->id ? 'selected' : '' }}>
                  {{ $quality->name }}
                </option>
              @endforeach
            </select>
          </div>
          
          <!-- Lọc theo danh hiệu thi đua -->
          <div>
            <select id="title_id" name="title_id" 
              class="w-full">
              <option value="">-- Tất cả danh hiệu --</option>
              @foreach($titles as $title)
                <option value="{{ $title->id }}" {{ isset($titleId) && $titleId == $title->id ? 'selected' : '' }}>
                  {{ $title->name }}
                </option>
              @endforeach
            </select>
          </div>
          
          <!-- Lọc theo trạng thái -->
          <div>
            <select id="status_id" name="status_id" 
              class="w-full">
              <option value="">-- Tất cả trạng thái --</option>
              @foreach($statuses as $status)
                <option value="{{ $status->id }}" {{ isset($statusId) && $statusId == $status->id ? 'selected' : '' }}>
                  {{ $status->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        
        <div class="flex justify-end gap-4">
          <button type="submit" class="btn btn-primary flex items-center">
            <span class="icomoon icon-filter mr-2"></span> Lọc
          </button>
          <a href="{{ route('evaluations.list', ['year' => $selectedYear]) }}" class="btn btn-additional flex items-center">
            <span class="icomoon icon-refresh mr-2"></span> Đặt lại
          </a>
        </div>
      </form>
    </div>
    
    <!-- Hiển thị thông tin lọc nếu có -->
    @if($searchName || isset($qualityId) && $qualityId || isset($titleId) && $titleId || isset($statusId) && $statusId)
    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-md" role="alert">
      <div class="flex items-center">
        <span class="icomoon icon-filter mr-2"></span>
        <p class="mr-2 mb-0">Kết quả lọc:</p>
        <div class="flex flex-wrap gap-2">
          @if($searchName)
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
              Tên: "{{ $searchName }}"
            </span>
          @endif
          
          @if(isset($qualityId) && $qualityId)
            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
              Xếp loại: {{ $qualities->where('id', $qualityId)->first()->name }}
            </span>
          @endif
          
          @if(isset($titleId) && $titleId)
            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">
              Danh hiệu: {{ $titles->where('id', $titleId)->first()->name }}
            </span>
          @endif
          
          @if(isset($statusId) && $statusId)
            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
              Trạng thái: {{ $statuses->where('id', $statusId)->first()->name }}
            </span>
          @endif
          
          @if(isset($filteredCount))
            <span class="text-gray-500 text-xs ml-2 self-center">Tìm thấy {{ $filteredCount }} kết quả</span>
          @endif
        </div>
      </div>
    </div>
    @endif

    <!-- Hiển thị thống kê chỉ dành cho trưởng đơn vị - giữ nguyên -->
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
              <p class="text-xl font-bold text-states-500">{{ $statistics['total'] }}</p>
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
      <p class="text-xs text-gray-500 text-right mb-0">{{ $statistics['approved']['percentage'] }}% đã được phê duyệt</p>
    </div>
    @endif
    
    @if (!$isOpenPeriod)
      <div class="alert flex p-4 mb-5 bg-yellow-50 border-l-4 border-yellow-500 rounded">
      <span class="icomoon icon-information-circle text-yellow-500 mr-3 text-xl"></span>
      <div>
        <span class="font-medium">Thông báo:</span> Năm học đã hết hạn phê duyệt.
      </div>
    </div>  
    @endif
    
    <!-- Bảng danh sách đánh giá - giữ nguyên -->
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
          <tr class="bg-white hover:bg-gray-50">
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
            <td colspan="8" class="text-center py-8">
              <div class="flex flex-col items-center justify-center text-gray-500">
                <span class="icomoon icon-search text-4xl mb-3"></span>
                <p class="font-medium">Không có đánh giá nào phù hợp với điều kiện lọc</p>
                @if($searchName || isset($qualityId) && $qualityId || isset($titleId) && $titleId || isset($statusId) && $statusId)
                  <p class="text-sm mt-2">Thử thay đổi điều kiện tìm kiếm hoặc <a href="{{ route('evaluations.list', ['year' => $selectedYear]) }}" class="text-blue-500 hover:underline">xóa bộ lọc</a></p>
                @else
                  <p class="text-sm mt-2">Không có đánh giá nào trong đơn vị.</p>
                @endif
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
    
    <!-- Phân trang -->
    <div class="flex justify-end mt-5">
      {{ $evaluations->links() }}
    </div>
  </div>
</section>
@endsection
