<div class="unit-evaluation-form-container">
  @if(!$isCurrentYear)
    <div class="alert flex p-4 mb-5 bg-blue-50 border-l-4 border-blue-500 rounded">
      <span class="icomoon icon-information-circle text-blue-500 mr-3 text-xl"></span>
      <div>
        <span class="font-medium">Thông báo:</span> Chưa có đánh giá đơn vị cho năm học này.
      </div>
    </div>
  @else
    <form action="{{ route('unit.evaluations.store') }}" method="POST" class="unit-evaluation-form bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      @csrf
      <h3 class="text-base font-semibold mb-5 text-states-600  border-b pb-3">Đánh giá đơn vị năm {{ now()->year - 1 }} - {{ now()->year }}</h3>
      
      <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="col-span-1">
          <label for="quality_id" class="block mb-2 font-bold text-states-600">
            <span class="flex items-center gap-2">
              <span class="icomoon icon-chart-bar"></span>
              <span>Xếp loại chất lượng </span>
              <span class="text-red-500">*</span>
            </span>
          </label>
          <select class="border-gray-300 border rounded-lg p-2.5 w-full focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50 transition-all" id="quality_id" name="quality_id" required>
            <option value="">-- Chọn xếp loại chất lượng --</option>
            @foreach($quality as $q)
              <option value="{{ $q->id }}" {{ $unitEvaluation && $unitEvaluation->quality_id == $q->id ? 'selected' : '' }}>
                {{ $q->name }}
              </option>
            @endforeach
          </select>
          <p class="mt-1 text-sm text-gray-500">Chọn xếp loại phù hợp với đơn vị</p>
        </div>
        
        <div class="mb-6 col-span-2">
          <label for="evidence" class="block mb-2 font-bold text-states-600">
            <span class="flex items-center gap-2">
              <span class="icomoon icon-tag"></span>
              <span>Minh chứng</span>
              <span class="text-red-500">*</span>
            </span>
          </label>
          <textarea id="evidence" name="evidence" class="ckeditor w-full border-gray-300 rounded-lg">{{ $unitEvaluation ? $unitEvaluation->evidence : '' }}</textarea>
          <p class="mt-1 text-sm text-gray-500">Liệt kê các minh chứng cho thành tích của đơn vị</p>
        </div>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="col-span-1">
          <div class="mb-6">
            <label for="title_id" class="block mb-2 font-bold text-states-600">
              <span class="flex items-center gap-2">
                <span class="icomoon icon-star"></span>
                <span>Danh hiệu thi đua</span>
                <span class="text-red-500">*</span>
              </span>
            </label>
            <select class="border-gray-300 border rounded-lg p-2.5 w-full focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50 transition-all" id="title_id" name="title_id" required>
              <option value="">-- Chọn danh hiệu thi đua --</option>
              @foreach($titles as $title)
                <option value="{{ $title->id }}" {{ $unitEvaluation && $unitEvaluation->title_id == $title->id ? 'selected' : '' }}>
                  {{ $title->name }}
                </option>
              @endforeach
            </select>
            <p class="mt-1 text-sm text-gray-500">Danh hiệu đơn vị đề xuất</p>
          </div>
          
          <div>
            <label for="reward_id" class="block mb-2 font-bold text-states-600">
              <span class="flex items-center gap-2">
                <span class="icomoon icon-gift"></span>
                <span>Đề xuất khen thưởng</span>
                <span class="text-red-500">*</span>
              </span>
            </label>
            <select class="border-gray-300 border rounded-lg p-2.5 w-full focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50 transition-all" id="reward_id" name="reward_id" required>
              <option value="">-- Chọn khen thưởng --</option>
              @foreach($rewards as $reward)
                <option value="{{ $reward->id }}" {{ $unitEvaluation && $unitEvaluation->reward_id == $reward->id ? 'selected' : '' }}>
                  {{ $reward->name }}
                </option>
              @endforeach
            </select>
            <p class="mt-1 text-sm text-gray-500">Hình thức khen thưởng phù hợp</p>
          </div>
        </div>
        
        <div class="mb-6 col-span-2">
          <label for="achievement" class="block mb-2 font-bold text-states-600">
            <span class="flex items-center gap-2">
              <span class="icomoon icon-shield-check"></span>
              <span>Thành tích đạt được</span>
              <span class="text-red-500">*</span>
            </span>
          </label>
          <textarea id="achievement" name="achievement" class="ckeditor w-full border-gray-300 rounded-lg" required>{{ $unitEvaluation ? $unitEvaluation->achievement : '' }}</textarea>
          <p class="mt-1 text-sm text-gray-500">Mô tả chi tiết thành tích mà đơn vị đã đạt được trong năm học</p>
        </div>
      </div>
      
      <div class="text-right border-t border-gray-200 pt-5">
        <button type="submit" class="btn btn-primary">
          {{ $unitEvaluation ? 'Cập nhật đánh giá' : 'Gửi đánh giá' }}
        </button>
      </div>
    </form>
    
    <div class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm">
      <div class="flex flex-col items-start">
        <div class="flex items-center gap-3">
          <span class="icomoon icon-speakerphone text-secondary-500"></span>
          <span class="font-medium text-secondary-500">Lưu ý khi đánh giá:</span>
        </div>
        <div class="pl-4">
          <ul class="list-disc pl-5 mt-1 text-gray-600">
            <li>Điền đầy đủ thông tin về thành tích của đơn vị</li>
            <li>Đính kèm các minh chứng cụ thể cho từng thành tích</li>
            <li>Đề xuất xếp loại và danh hiệu phù hợp với thành tích</li>
          </ul>
        </div>
      </div>
    </div>
  @endif
</div>