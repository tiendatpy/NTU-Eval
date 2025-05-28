@extends('layouts.app')

@section('title', 'Duyệt Đánh Giá Đơn Vị')

@section('content')
<section class="mod-unit-eval-approval bg-white rounded-2xl custom-box-shadow py-8">
  <div class="container">
    <!-- Header với hiệu ứng gradient -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 pb-6 border-b border-gray-200">
      <div class="mb-4 md:mb-0">
        <h2 class="text-base font-bold mb-2">Duyệt Đánh Giá Đơn Vị</h2>
        <p class="text-gray-500">{{ auth()->user()->unit->name }}</p>
      </div>
      
      <div class="flex items-center gap-3">
        <form id="yearFilterForm" action="{{ route('unit-evaluations.approve') }}" method="GET" class="flex items-center gap-3">
          <label for="year" class="font-medium">Năm:</label>
          <select id="year" name="year" class="border border-gray-300 rounded-md px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500" onchange="this.form.submit()">
            @foreach($years as $year)
              <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }} - {{ $year+1 }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </div>

    <!-- Thông tin đánh giá đơn vị hiện tại -->
    @if(!$unitEvaluation)
    <div class="py-16 text-center bg-gray-50 rounded-2xl border border-dashed border-gray-300">
      <div class="mb-5 inline-block p-5 bg-gray-100 rounded-full">
        <span class="icomoon icon-document-text text-3xl"></span>
      </div>
      <h3 class="text-lg font-semibold text-primary-800 mb-2">Chưa có đánh giá nào</h3>
      <p class="text-gray-500 max-w-md mx-auto mb-6">Đơn vị chưa có đánh giá cho năm học {{ $selectedYear }} - {{ $selectedYear+1 }}</p>
      @if(auth()->user()->role->isUnitLeader || auth()->user()->role->isSuperAdmin)
        <a href="{{ route('unit.evaluations.index', ['year' => $selectedYear]) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md custom-box-shadow text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
          Tạo đánh giá đơn vị
        </a>
      @endif
    </div>
    @else
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 transition-all duration-300 hover:shadow-lg border border-gray-100">
      <!-- Thông tin cơ bản - hiển thị dạng card nổi bật -->
      <div class="bg-gradient-to-r from-primary-50 to-primary-100 p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-base font-semibold mb-4">{{ $unitEvaluation->unit->name }}</h3>
            <p class="text-gray-600 text-sm">Năm học {{ $selectedYear }} - {{ $selectedYear+1 }}</p>
          </div>
          <div>
            @if($unitEvaluation->is_approved)
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-500" fill="currentColor" viewBox="0 0 8 8">
                  <circle cx="4" cy="4" r="3" />
                </svg>
                Đã phê duyệt
              </span>
            @else
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-500" fill="currentColor" viewBox="0 0 8 8">
                  <circle cx="4" cy="4" r="3" />
                </svg>
                Chờ phê duyệt
              </span>
            @endif
          </div>
        </div>
      </div>
      
      <!-- Thông tin chung -->
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <!-- Cột 1: Thông tin cơ bản -->
          <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
            <h4 class="font-semibold mb-3 pb-2 border-b">Thông tin chung</h4>
            <div class="space-y-3">
              <div class="flex items-center">
                <span class="text-primary-700 font-semibold text-sm">Thời gian đánh giá:</span>
                <span class="text-sm font-medium text-primary-500 ml-4">{{ format_date($unitEvaluation->created_at, 'd/m/y', true) }}</span>
              </div>
              <div class="flex items-center">
                <span class="text-primary-700 font-semibold text-sm">Người đánh giá:</span>
                <span class="text-sm font-medium text-primary-500 ml-4">{{ $unitEvaluation->evaluator->full_name ?? 'N/A' }}</span>
              </div>
              @if($unitEvaluation->approved_at)
              <div class="flex items-center">
                <span class="text-primary-700 font-semibold text-sm">Ngày duyệt:</span>
                <span class="text-sm font-medium text-primary-500">{{ format_date($unitEvaluation->approved_at, 'd/m/y', true) }}</span>
              </div>
              <div class="flex items-center">
                <span class="text-primary-700 font-semibold text-sm">Người duyệt:</span>
                <span class="text-sm font-medium text-primary-500">{{ auth()->user()->full_name ?? 'N/A' }}</span>
              </div>
              @endif
            </div>
          </div>
          
          <!-- Cột 2: Tự đánh giá -->
          <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100">
            <h4 class="font-semibold  mb-3 pb-2 border-b">Tự đánh giá</h4>
            <div class="space-y-3">
              <div>
                <span class="text-primary-700 font-semibold text-sm block mb-1">Xếp loại:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-blue-50 text-blue-700 border border-blue-100">
                  {{ $unitEvaluation->quality->name }}
                </span>
              </div>
              <div>
                <span class="text-primary-700 font-semibold text-sm block mb-1">Danh hiệu:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                  {{ $unitEvaluation->title->name }}
                </span>
              </div>
              <div>
                <span class="text-primary-700 font-semibold text-sm block mb-1">Khen thưởng:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-purple-50 text-purple-700 border border-purple-100">
                  {{ $unitEvaluation->reward->name }}
                </span>
              </div>
            </div>
          </div>
          
          <!-- Cột 3: Đã phê duyệt -->
          <div class="bg-white p-4 rounded-lg custom-box-shadow border border-gray-100 {{ !$unitEvaluation->is_approved ? 'opacity-60' : '' }}">
            <h4 class="font-semibold  mb-3 pb-2 border-b">Đã phê duyệt</h4>
            @if($unitEvaluation->is_approved)
            <div class="space-y-3">
              <div>
                <span class="text-gray-500 text-sm block mb-1">Người duyệt:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium">
                  {{ $unitEvaluation->approver->full_name ?? 'N/A' }}
                </span>
              <div>
                <span class="text-gray-500 text-sm block mb-1">Xếp loại:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-green-50 text-green-700 border border-green-100">
                  {{ $unitEvaluation->approvedQuality->name }}
                </span>
              </div>
              <div>
                <span class="text-gray-500 text-sm block mb-1">Danh hiệu:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                  {{ $unitEvaluation->approvedTitle->name }}
                </span>
              </div>
              <div>
                <span class="text-gray-500 text-sm block mb-1">Khen thưởng:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-teal-50 text-teal-700 border border-teal-100">
                  {{ $unitEvaluation->approvedReward->name }}
                </span>
              </div>
            </div>
            @else
            <div class="h-full flex items-center justify-center">
              <div class="text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-400 text-sm">Chưa được phê duyệt</p>
              </div>
            </div>
            @endif
          </div>
        </div>
        
        <!-- Thành tích -->
        <div class="bg-white rounded-lg custom-box-shadow border border-gray-100 overflow-hidden mb-6">
          <div class="flex items-center bg-gray-50 px-4 py-3 border-b border-gray-200">
            <span class="icomoon icon-sparkles text-states-500 mr-3"></span>
            <h5 class="font-semibold mb-0">Thành tích nổi bật</h5>
          </div>
          
          <div class="p-5">
            <div class="mb-6">
              <h6 class="text-sm font-medium text-primary-700 mb-3 pb-2 border-b border-gray-100">Thành tích đơn vị tự đánh giá</h6>
              <div class="prose prose-sm max-w-none bg-blue-50 p-4 rounded-lg">
                {!! $unitEvaluation->achievement !!}
              </div>
            </div>
            
            @if($unitEvaluation->approved_achievement)
            <div>
              <h6 class="text-sm font-medium text-primary-700 mb-3 pb-2 border-b border-gray-100">Thành tích đã được phê duyệt</h6>
              <div class="prose prose-sm max-w-none bg-green-50 p-4 rounded-lg">
                {!! $unitEvaluation->approved_achievement !!}
              </div>
            </div>
            @endif
          </div>
        </div>
        
        <!-- Minh chứng -->
        @if($unitEvaluation->evidence)
        <div class="bg-white rounded-lg custom-box-shadow border border-gray-100 overflow-hidden mb-6">
          <div class="flex items-center bg-gray-50 px-4 py-3 border-b border-gray-200">
            <span class="icomoon icon-document-text text-states-500 mr-3"></span>
            <h5 class="font-semibold mb-0">Minh chứng</h5>
          </div>
          
          <div class="p-5">
            <div class="mb-6">
              <h6 class="text-sm font-medium text-gray-700 mb-3 pb-2 border-b border-gray-100">Minh chứng đơn vị tự đánh giá</h6>
              <div class="prose prose-sm max-w-none bg-blue-50 p-4 rounded-lg">
                {!! $unitEvaluation->evidence !!}
              </div>
            </div>
            
            @if($unitEvaluation->approved_evidence)
            <div>
              <h6 class="text-sm font-medium text-gray-700 mb-3 pb-2 border-b border-gray-100">Minh chứng đã được phê duyệt</h6>
              <div class="prose prose-sm max-w-none bg-green-50 p-4 rounded-lg">
                {!! $unitEvaluation->approved_evidence !!}
              </div>
            </div>
            @endif
          </div>
        </div>
        @endif
      </div>
      
      <!-- Phần thao tác -->
      @if(!$unitEvaluation->is_approved)
      <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
        <button id="openApprovalFormBtn" type="button" class="btn btn-primary">
          Phê duyệt đánh giá
        </button>
      </div>
      @else
      <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
        <button id="openApprovalFormBtn" type="button" class="btn btn-primary">
          Cập nhật
        </button>
      </div>
      @endif
    </div>
    @endif
  </div>
</section>

<!-- Form phê duyệt (Cải tiến) - Hỗ trợ cả phê duyệt mới và cập nhật -->
@if($unitEvaluation)
<div id="approvalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 backdrop-blur-sm transition-opacity duration-300">
  <div class="relative top-20 mx-auto p-0 border-0 w-11/12 md:w-3/4 lg:w-2/3 max-w-4xl shadow-2xl rounded-lg bg-white overflow-hidden transition-transform duration-300 transform scale-95 opacity-0" id="modalContent">
    <div class="bg-states-400 text-white px-6 py-4 flex justify-between items-center">
      <h3 class="text-base text-white mb-0">
        {{ $unitEvaluation->is_approved ? 'Cập nhật phê duyệt đánh giá' : 'Phê duyệt đánh giá đơn vị' }}
      </h3>
      <button id="closeModal" class="text-white hover:text-gray-200 focus:outline-none transition-transform duration-200 transform hover:scale-110">
        <span class="icomoon icon-close text-xl"></span>
      </button>
    </div>
    
    <div id="approvalForm" class="p-6">
      <form action="{{ route('unit-evaluations.approve-submit', $unitEvaluation->id) }}" method="POST">
        @csrf
        
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-md">
          <div class="flex items-center">
            <div class="flex">
              <span class="icomoon icon-information-circle text-blue-800 text-lg"></span>
            </div>
            <div class="ml-3">
              <p class="text-sm text-blue-800 mb-0">
                Bạn đang {{ $unitEvaluation->is_approved ? 'cập nhật phê duyệt' : 'phê duyệt' }} đánh giá đơn vị <strong>{{ $unitEvaluation->unit->name }}</strong> cho năm học <strong>{{ $selectedYear }} - {{ $selectedYear+1 }}</strong>
              </p>
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div>
            <label for="approved_quality_id" class="block text-sm font-medium text-gray-700 mb-1">Xếp loại chất lượng:</label>
            <div class="relative">
              <select id="approved_quality_id" name="approved_quality_id" class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg custom-box-shadow focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required>
                <option value="">-- Chọn xếp loại --</option>
                @foreach($qualities as $quality)
                  <option value="{{ $quality->id }}" {{ ($unitEvaluation->approved_quality_id == $quality->id) || (!$unitEvaluation->is_approved && $unitEvaluation->quality_id == $quality->id) ? 'selected' : '' }}>
                    {{ $quality->name }}
                  </option>
                @endforeach
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
              </div>
            </div>
          </div>
          
          <div>
            <label for="approved_title_id" class="block text-sm font-medium text-gray-700 mb-1">Danh hiệu thi đua:</label>
            <div class="relative">
              <select id="approved_title_id" name="approved_title_id" class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg custom-box-shadow focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required>
                <option value="">-- Chọn danh hiệu --</option>
                @foreach($titles as $title)
                  <option value="{{ $title->id }}" {{ ($unitEvaluation->approved_title_id == $title->id) || (!$unitEvaluation->is_approved && $unitEvaluation->title_id == $title->id) ? 'selected' : '' }}>
                    {{ $title->name }}
                  </option>
                @endforeach
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
              </div>
            </div>
          </div>
          
          <div>
            <label for="approved_reward_id" class="block text-sm font-medium text-gray-700 mb-1">Hình thức khen thưởng:</label>
            <div class="relative">
              <select id="approved_reward_id" name="approved_reward_id" class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg custom-box-shadow focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required>
                <option value="">-- Chọn khen thưởng --</option>
                @foreach($rewards as $reward)
                  <option value="{{ $reward->id }}" {{ ($unitEvaluation->approved_reward_id == $reward->id) || (!$unitEvaluation->is_approved && $unitEvaluation->reward_id == $reward->id) ? 'selected' : '' }}>
                    {{ $reward->name }}
                  </option>
                @endforeach
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mb-6">
          <label for="approved_achievement" class="block text-sm font-medium text-gray-700 mb-1">Thành tích nổi bật (phê duyệt):</label>
          <div class="mt-1 relative rounded-md custom-box-shadow">
            <textarea id="approved_achievement" name="approved_achievement" class="ckeditor focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md resize-none" required>{!! $unitEvaluation->is_approved ? $unitEvaluation->approved_achievement : $unitEvaluation->achievement !!}</textarea>
          </div>
        </div>
        
        <div class="mb-6">
          <label for="approved_evidence" class="block text-sm font-medium text-gray-700 mb-1">Minh chứng (phê duyệt):</label>
          <div class="mt-1 relative rounded-md custom-box-shadow">
            <textarea id="approved_evidence" name="approved_evidence" class="ckeditor focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md resize-none">{!! $unitEvaluation->is_approved ? $unitEvaluation->approved_evidence : $unitEvaluation->evidence !!}</textarea>
          </div>
        </div>
        
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
          <button type="button" id="cancelApproval" class="btn btn-tertiary">
            Hủy
          </button>
          <button type="submit" class="btn btn-secondary">
            {{ $unitEvaluation->is_approved ? 'Cập nhật' : 'Phê duyệt' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endsection