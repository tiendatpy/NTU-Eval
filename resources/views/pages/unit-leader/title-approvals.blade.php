@extends('layouts.app')

@section('title', 'Xét Duyệt Danh Hiệu Thi Đua')

@section('content')
  <section class="mod-title-approval bg-white">
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
              <th class="w-10p">Tên cá nhân</th>
              <th class="w-10p">Danh Hiệu</th>
              <th class="w-10p">Hình Thức <br> Khen Thưởng</th>
              <th class="w-20p">Tóm Tắt Thành Tích</th>
              <th class="w-15p">Tự Nhận Xét</th>
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
                <td>{{ $nomination->review }}</td>
                <td>{{ $nomination->achievement }}</td>
                <td>{{ $nomination->comment }}</td>
                <td>
                  <div class="flex flex-col gap-3">
                    <!-- Phần chọn danh hiệu -->
                    <div class="title-approval bg-white rounded-lg p-3 shadow-sm">
                      <div class="flex flex-col gap-2">
                        <label class="font-medium text-gray-700 flex items-center">
                          <span class="icomoon icon-ticket mr-2 text-primary-600"></span>
                          Danh hiệu
                        </label>
                        <input type="hidden" name="nominations[{{ $index }}][id]" value="{{ $nomination->id }}">
                        <select name="nominations[{{ $index }}][title_id]" 
                                class="border border-gray-300 rounded-md p-2 w-full focus:ring-2 focus:ring-primary-500 focus:border-primary-500 approve-title">
                          @foreach ($titles as $title)
                            <option value="{{ $title->id }}" 
                                    {{ ($nomination->approved_title_id ? $nomination->approved_title_id : $nomination->title_id) == $title->id ? 'selected' : '' }}>
                              {{ $title->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <!-- Phần feedback -->
                    <div class="feedback-section mx-auto">
                      <button type="button" 
                              class="btn btn-additional edit-btn flex items-center justify-center gap-2" 
                              data-id="{{ $nomination->id }}"   
                              data-feedback="{{ $nomination->feedback }}"
                              title="Thêm nhận xét">
                        <span class="icomoon icon-pencil-alt"></span>
                        <span>Nhận xét</span>
                      </button>
                    </div>
                  </div>
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
            <a href="{{ route('evaluations.export-title-nominations') }}" class="btn btn-secondary inline-flex items-center gap-3">
              <span class="icomoon icon-download-v2 text-xl"></span>
              <span>Xuất File</span>
            </a>
            <button type="submit" class="btn btn-primary {{ $allApproved ? 'bg-states-600/30 hover:bg-states-600/30  cursor-not-allowed' : '' }}" 
                    {{ $allApproved ? 'disabled' : '' }}>
              <span>{{ $allApproved ? 'Đã phê duyệt' : 'Xác nhận' }}</span>
            </button>
          </div>
        @endif
      </form>
      <!-- Phân trang -->
      <div class="mt-4">
        {{ $nominations->links() }}
      </div>
    </div>
    <!-- Phần popup nên được đặt bên ngoài form chính -->
    <form action="#" method="POST" class="edit-feedback-form">
      @csrf
      <div class="overlay js-popup fixed inset-0 bg-black/25 hidden">
        <!-- Popup -->
        <div class="feedback-popup absolute min-w-[400px] lg:w-[600px] transform-center-middle bg-white shadow-slate-600 rounded-lg p-4 z-50">
          <table class="w-full">
            <tr class="hover:bg-white border-b">
              <td class="font-semibold p-5 text-primary-800">Nhận xét ưu, khuyết điểm</td>
              <td class="p-5 text-right text-2xl" >
                <button type="button" class="hover:text-states-500 close-popup"><span class="icomoon icon-close"></span></button>
              </td>
            </tr>
          </table>
          <div class="bg-white mt-5">
            <table class="mx-auto">
              <textarea class="ckeditor feedback-nomination" name="feedback" id="feedback-nomination">
              </textarea>
            </table>
          </div>
            <div class="mt-4 text-right">
              <button type="button" class="btn btn-secondary mr-2 close-popup">
                <span>Hủy bỏ</span>
              </button>
              <button type="submit" class="btn btn-primary">
                <span>Xác nhận</span>
              </button>
            </div>
          </div>
      </div>
    </form>
  </section>

@endsection