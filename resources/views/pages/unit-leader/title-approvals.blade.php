@extends('layouts.app')

@section('title', 'Xét Duyệt Danh Hiệu Thi Đua')

@section('content')
  <section class="mod-all-title-nominations bg-white">
    <div class="container py-8">
      <div class="flex justify-between items-center mb-7">
        <h2 class="text-base mb-0">Danh Sách Xét Duyệt Danh Hiệu Thi Đua</h2>
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

      <form action="{{ route('evaluations.approve-titles') }}" method="POST" id="approvalForm">
        @csrf
        
        <table class="w-full text-sm overflow-hidden">
          <thead class="bg-states-300">
            <tr>
              <th class="w-5p">STT</th>
              <th class="w-15p">Tên cá nhân</th>
              <th class="w-10p">Danh Hiệu</th>
              <th class="w-15p">Hình Thức <br> Khen Thưởng</th>
              <th class="w-25p">Tóm Tắt Thành Tích</th>
              <th class="w-15p">Góp Ý</th>
              <th class="w-15p">Duyệt</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($nominations as $index => $nomination)
              <tr class="bg-white">
                <td>{{ $index + 1 }}</td>
                <td>{{ $nomination->evaluator->full_name }}</td>
                <td>{{ $nomination->title->name }}</td>
                <td>{{ $nomination->reward->name }}</td>
                <td>{{ $nomination->achievement }}</td>
                <td>{{ $nomination->review }}</td>
                <td>
                  <input type="hidden" name="nominations[{{ $index }}][id]" value="{{ $nomination->id }}">
                  <select name="nominations[{{ $index }}][title_id]" class="border-primary-300 border-1 p-2 w-full rounded-lg approve-title">
                    @foreach ($titles as $title)
                      <option value="{{ $title->id }}" {{ ($nomination->approved_title_id ? $nomination->approved_title_id : $nomination->title_id) == $title->id ? 'selected' : '' }}>
                        {{ $title->name }}
                      </option>
                    @endforeach
                  </select>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4">Không có đề xuất danh hiệu nào cần phê duyệt.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        @if ($nominations->count() > 0)
          @php
          $allApproved = $nominations->filter(function($nomination) {
              return $nomination->evaluator->role->name !== 'Trưởng đơn vị';
          })->every(function($nomination) {
              return !is_null($nomination->approved_title_id);
          });
          @endphp
          <div class="text-right mt-5">
            <a href="" class="btn btn-secondary inline-flex items-center gap-3">
              <span class="icomoon icon-download-v2 text-xl"></span>
              <span>Xuất File</span>
            </a>
            <button type="submit" class="btn btn-primary {{ $allApproved ? 'bg-states-600/80  cursor-not-allowed' : '' }}" 
                    {{ $allApproved ? 'disabled' : '' }}>
              <span>{{ $allApproved ? 'Đã phê duyệt' : 'Xác nhận' }}</span>
            </button>
          </div>
        @endif
      </form>

      <!-- Phân trang -->
      <div class="mt-4">
        {{ $nominations->appends(['year' => $selectedYear])->links() }}
      </div>
    </div>
  </section>
@endsection