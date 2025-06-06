<!-- filepath: e:\WORKSPACE\ntu-eval\resources\views\pages\unit-leader\period-management.blade.php -->
@extends('layouts.app')

@section('title', 'Quản Lý Đợt Đánh Giá')

@section('content')
<section class="mod-period-management bg-white rounded-2xl custom-box-shadow py-8">
  <div class="container">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
      <div class="mb-4 md:mb-0">
        <h2 class="text-base font-bold mb-2">Quản Lý Đợt Đánh Giá</h2>
        <p class="text-gray-500">{{ auth()->user()->unit->name }}</p>
      </div>
      
      <div>
        <a href="{{ route('periods.create') }}" class="btn btn-primary flex items-center">
          <span class="icomoon icon-plus mr-2"></span>
          Tạo đợt đánh giá mới
        </a>
      </div>
    </div>
    
    <!-- Bảng danh sách đợt đánh giá -->
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-states-300">
            <th>STT</th>
            <th>Đợt đánh giá</th>
            <th>Thời hạn đánh giá</th>
            <th>Trạng thái</th>
            <th>Người cập nhật cuối</th>
            <th>Thời gian cập nhật</th>
            <th class="text-center">Hành động</th>
          </tr>
        </thead>
        <tbody>
          @forelse($periods as $index => $period)
          <tr class="border-b hover:bg-gray-50">
            <td>{{$index+1}}</td>
            <td>{{ $period->year }} - {{ $period->year + 1 }}</td>
            <td>
              {{ $period->start_date->format('d/m/Y') }} - {{ $period->end_date->format('d/m/Y') }}
            </td>
            <td>
              <span class="px-2 py-1 rounded-full text-sm status-badge
                @if($period->status_id == $openStatusId) bg-green-100 text-green-800 
                @elseif($period->status_id == $closedStatusId) bg-red-100 text-red-800 
                @else bg-yellow-100 text-yellow-800 
                @endif">
                {{ $period->status->name }}
              </span>
            </td>
            <td>
              {{ $period->createdBy->full_name ?? 'N/A' }}
            </td>
            <td>
              <span>{{ format_date($period->updated_at, 'd/m/Y', true) }}</span>
            </td>
            <td class="p-3 text-center">
              <a href="{{ route('periods.edit', $period->id) }}" class="text-blue-600 hover:text-blue-800" title="Chỉnh sửa">
                <span class="icomoon icon-pencil-alt text-xl"></span>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="p-3 text-center text-gray-500">Không có đợt đánh giá nào</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <div class="mt-4">
      {{ $periods->links() }}
    </div>
  </div>
</section>


@endsection