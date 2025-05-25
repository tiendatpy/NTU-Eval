@extends('layouts.app')

@section('title', 'Chi tiết đánh giá')

@section('content')
<section class="mod-self-eval-result bg-white rounded-2xl py-8">
  <div class="container">
    <div class="mb-8 text-sm flex justify-between items-center">
      <h2 class="mb-0 text-base">Chi Tiết Đánh Giá</h2>
      <div>
        <a href="{{ route('evaluations.list') }}" class="btn btn-secondary mr-2">Quay lại</a>
      </div>
    </div>

    <div class="mb-5 p-4 bg-blue-50 border-l-4 border-blue-600 rounded-lg">
      <div class="flex flex-wrap">
        <div class="w-full md:w-1/3 mb-2 md:mb-0">
          <strong>Viên chức:</strong> {{ $evaluation->evaluator->full_name }}
        </div>
        <div class="w-full md:w-1/3 mb-2 md:mb-0">
          <strong>Đơn vị:</strong> {{ $evaluation->unit->name }}
        </div>
        <div class="w-full md:w-1/3 mb-2 md:mb-0">
          <strong>Trạng thái:</strong> {{ $evaluation->status->name }}
        </div>
      </div>
    </div>
    <div id="evaluation-content">
      <div class="self-table mb-5">
        <div class="self-evaluation mb-5">
          <h3 class="mb-5">I. TỰ ĐÁNH GIÁ</h3>
          <table class="w-full text-sm overflow-x-auto">
            <thead class="rounded-t-xl">
              <tr class="bg-states-300">
                <th class="w-5p">STT</th>
                <th class="w-30p">Nội dung đánh giá</th>
                <th class="w-45p">Kê khai, minh chứng</th>
                <th class="w-10p">Mức đạt được</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($evaluation->details as $detail)
              <tr class="bg-white">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $detail->criteria->name }}</td>
                <td>{!! $detail->evidence !!}</td>
                <td>
                  {{ $detail->rating == 4 ? 'Xuất sắc' : ($detail->rating == 3 ? 'Tốt' : ($detail->rating == 2 ? 'Trung bình' : 'Yếu')) }}
                </td>
              </tr>
              @endforeach
              <tr>
                <td colspan="2"></td>
                <td colspan="2">
                  <div class="flex justify-between">
                    <span class="inline-block w-2/3 font-bold">Xếp loại chất lượng:</span>
                    <div class="w-1/3 text-right">
                      {{ $evaluation->quality->name }}
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="self-rating mb-5">
          <h3 class="mb-5">II. TỰ NHẬN XÉT</h3>
          <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            {!! $evaluation->comment !!}
          </div>
        </div>

        <div class="self-title-nomination mb-5">
          <h3 class="mb-5">III. ĐỀ XUẤT DANH HIỆU THI ĐUA, HÌNH THỨC KHEN THƯỞNG</h3>
          <div>
            <table class="w-full text-sm overflow-hidden" id="nominations-table">
              <thead class="rounded-t-xl">
                <tr class="bg-states-300">
                  <th class="w-20p">Đề xuất danh hiệu</th>
                  <th class="w-20p">Hình thức khen thưởng</th>
                  <th class="w-50p">Tóm tắt thành tích</th>
                </tr>
              </thead>
              <tbody>
                <tr class="bg-white">
                  <td>{{ $evaluation->title->name }}</td>
                  <td>{{ $evaluation->reward->name }}</td>
                  <td>{!! $evaluation->achievement !!}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        @if(!$isUnitLeader && $evaluation->status->name !== 'Đã phê duyệt')
          <div class="user-review mb-5">
            <h3 class="mb-5">IV. GÓP Ý</h3>
            <form action="{{ route('all-evaluations.add-review', $evaluation->id) }}" method="post">
              @csrf
              <div class="mb-4">
                <label for="review" class="block mb-2 font-medium">Thêm góp ý của bạn:</label>
                <textarea id="review" name="review" class="ckeditor border-primary-500 border-1 p-2 w-full rounded-lg" rows="5">{{ $evaluation->review }}</textarea>
              </div>
              
              <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">
                  {{ $evaluation->review ? 'Cập nhật góp ý' : 'Gửi góp ý' }}
                </button>
              </div>
            </form>
          </div>
        @else
          <div class="user-review mb-5">
            <h3 class="mb-5">IV. GÓP Ý</h3>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
              @if ($evaluation->review)
                {!! $evaluation->review !!}
              @else
                <span class="text-gray-500">Chưa có góp ý từ người đánh giá.</span>
              @endif
            </div>
          </div>
        @endif
        
        @if($isUnitLeader)
        <form action="{{ route('all-evaluations.approve-details', $evaluation->id) }}" method="post">
          @csrf
          <div class="approval-section mb-5">
            <h3 class="mb-5">V. ĐÁNH GIÁ CỦA TRƯỞNG ĐƠN VỊ</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
              <div>
                <label for="approved_quality_id" class="block mb-2 font-bold">Xếp loại chất lượng:</label>
                <select id="approved_quality_id" name="approved_quality_id" class="border-primary-300 border-1 px-5 py-4 w-full rounded-lg" required>
                  <option value="">-- Chọn xếp loại --</option>
                  @foreach($qualities as $quality)
                    <option value="{{ $quality->id }}" {{ $evaluation->approved_quality_id == $quality->id ? 'selected' : '' }}>
                      {{ $quality->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              
              <div>
                <label for="approved_title_id" class="block mb-2 font-bold">Danh hiệu thi đua:</label>
                <select id="approved_title_id" name="approved_title_id" class="border-primary-300 border-1 px-5 py-4 w-full rounded-lg" required>
                  <option value="">-- Chọn danh hiệu --</option>
                  @foreach($titles as $title)
                    <option value="{{ $title->id }}" {{ $evaluation->approved_title_id == $title->id ? 'selected' : '' }}>
                      {{ $title->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            
            <div class="mb-5">
              <label for="feedback" class="block mb-2 font-bold">Nhận xét:</label>
              <textarea id="feedback" name="feedback" class="ckeditor border-primary-500 border-1 p-2 w-full rounded-lg">{{ $evaluation->feedback }}</textarea>
            </div>
            
            <div class="flex justify-end">
              <button type="submit" class="btn btn-primary">
                {{ $evaluation->status->name === 'Đã phê duyệt' ? 'Cập nhật' : 'Phê duyệt' }}
              </button>
            </div>
          </div>
        </form>
        @elseif($evaluation->status->name === 'Đã phê duyệt')
        <div class="approval-results mb-5">
          <h3 class="mb-5">V. ĐÁNH GIÁ CỦA TRƯỞNG ĐƠN VỊ</h3>
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
            @if($evaluation->approved_quality_id)
            <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
              <h4 class="text-sm font-medium text-gray-600 mb-2">Xếp loại được phê duyệt:</h4>
              <p class="text-base font-semibold">
                <span class="inline-flex items-center">
                  <span class="icomoon icon-medal text-yellow-500 mr-2"></span>
                  {{ $evaluation->approvedQuality->name }}
                </span>
              </p>
            </div>
            @endif
            
            @if($evaluation->approved_title_id)
            <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-indigo-500">
              <h4 class="text-sm font-medium text-gray-600 mb-2">Danh hiệu được phê duyệt:</h4>
              <p class="text-base font-semibold">
                <span class="inline-flex items-center">
                  <span class="icomoon icon-award text-indigo-500 mr-2"></span>
                  {{ $evaluation->approvedTitle->name }}
                </span>
              </p>
            </div>
            @endif
          </div>
        </div>
        @endif

        @if($isUnitLeader)
          <div class="export-file-btn text-right mt-6">
            <a href="{{ route('evaluations.export', $evaluation->id) }}" class="btn btn-secondary inline-flex items-center gap-3">
              <span class="icomoon icon-download-v2 text-xl"></span>
              <span>Xuất File</span>
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endsection