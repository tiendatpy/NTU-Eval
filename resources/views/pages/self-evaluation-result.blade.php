@extends('layouts.app')

@section('title', 'Kết Quả Đánh Giá')

@section('content')
  <section class="mod-self-eval-result bg-white rounded-2xl py-8">
    <div class="container">
      <div class="mb-8 text-sm">
        <h2 class="mb-7 text-base">Kết Quả Đánh Giá</h2>        
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
                    {{ $detail->score == 4 ? 'Xuất sắc' : ($detail->score == 3 ? 'Tốt' : ($detail->score == 2 ? 'Trung bình' : 'Yếu')) }}
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
          
          @if ($evaluation->feedback)
          <div class="self-review mb-5">
            <h3 class="mb-5">IV. BÌNH XÉT CỦA TRƯỞNG ĐƠN VỊ</h3>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
              {!! $evaluation->feedback !!}
            </div>
          </div>
          @endif

          @if ($evaluation->approved_quality_id)
          <div class="approved-quality mb-5">
            <h3 class="mb-5">V. XẾP LOẠI ĐƯỢC PHÊ DUYỆT</h3>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
              {{ $evaluation->approvedQuality->name }}
            </div>
          </div>
          @endif

          @if ($evaluation->approved_title_id)
          <div class="approved-title-nomination mb-5">
            <h3 class="mb-5">VI. DANH HIỆU ĐƯỢC PHÊ DUYỆT</h3>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
              {{ $evaluation->approvedTitle->name }}
            </div>
          </div>
          @endif
          <div class="export-file-btn text-right">
            <a href="{{ route('evaluations.export', $evaluation->id) }}" class="btn btn-secondary inline-flex items-center gap-3">
              <span class="icomoon icon-download-v2 text-xl"></span>
              <span>Xuất File</span>
            </a>
            <a href="{{ route('evaluations.index') }}" class="btn btn-primary">Quay lại</a>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection