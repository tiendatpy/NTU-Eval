@if ($isOpenPeriod)
  <form action="{{ route('evaluations.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="self-table mb-5">
      <div class="self-evaluation mb-5">
        <h3 class="mb-5">I. TỰ ĐÁNH GIÁ</h3>
        <table class="w-full text-sm overflow-hidden">
          <thead class="rounded-t-xl">
            <tr class="bg-states-300">
              <th class="w-5p">STT</th>
              <th class="w-30p">Nội dung đánh giá</th>
              <th class="w-60p">Kê khai, minh chứng (nếu có)</th>
              <th class="w-5p">Mức đạt được</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($criteria as $cri)
                @php
                  $existingDetail = $evaluation ? $evaluation->details->firstWhere('criteria_id', $cri->id) : null;
                  $evidence = $existingDetail ? $existingDetail->evidence : '';
                  $rating = $existingDetail ? $existingDetail->rating : 4;
                @endphp
              <tr class="bg-white">
                <td>{{ $loop->iteration }}</td>
                <td>
                  <input type="hidden" name="details[{{ $loop->index }}][criteria_id]" value="{{ $cri->id }}">
                  {{ $cri->name }}
                </td>
                <td>
                  <textarea class="ckeditor" name="details[{{ $loop->index }}][evidence]" 
                    class="border-primary-500 border-1 p-2 w-full rounded-lg" 
                    placeholder="Nhập thông tin minh chứng (nếu có)">{{ $evidence }}
                  </textarea>
                </td>
                <td>
                  <select name="details[{{ $loop->index }}][rating]" class="border-primary-300 border-1 px-5 py-4 w-auto rounded-lg w-50 rating" required>
                    <option value="4" {{ $rating == 4 ? 'selected' : '' }}>Xuất sắc</option>
                    <option value="3" {{ $rating == 3 ? 'selected' : '' }}>Tốt</option>
                    <option value="2" {{ $rating == 2 ? 'selected' : '' }}>Trung bình</option>
                    <option value="1" {{ $rating == 1 ? 'selected' : '' }}>Yếu</option>
                  </select>
                </td>
              </tr>
            @endforeach
            <tr>
              <td colspan="4">
                <div class="text-right">
                  <span class="font-bold">Điểm đánh giá:</span>
                  <span class="total-rating">{{ $evaluation ? number_format($evaluation->rating, 2) : '4' }}</span>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="2"></td>
              <td colspan="2">
                <div class="flex justify-between">
                  <span class="w-2/3 font-bold flex justify-end">Tự xếp loại chất lượng:</span>
                  <div class="w-1/3 flex justify-end">
                    <select name="quality_id" class="border-primary-300 border-1 px-5 py-4 w-auto rounded-lg" required>
                      @foreach ($quality as $qual)
                        <option value="{{ $qual->id }}" {{ ($evaluation && $evaluation->quality_id == $qual->id) ? 'selected' : '' }}>{{ $qual->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="self-rating mb-5">
        <h3 class="mb-5">II. TỰ NHẬN XÉT</h3>
        <div>
          <textarea name="comment" id="comment" class="ckeditor">{{ $evaluation ? $evaluation->comment : '' }}</textarea>
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
            <tr>
              <td>
                <select name="title_id" class="border-primary-300 border-1 px-5 py-4 w-full rounded-lg" required>
                  @foreach ($titles as $title)
                    <option value="{{ $title->id }}"{{ ($evaluation && $evaluation->title_id == $title->id) ? 'selected' : '' }}>{{ $title->name }}</option>
                  @endforeach
                </select>
              </td>
              <td>
                <select name="reward_id" class="border-primary-300 border-1 px-5 py-4 w-full rounded-lg" required>
                  @foreach ($rewards as $reward)
                    <option value="{{ $reward->id }}" {{ ($evaluation && $evaluation->reward_id == $reward->id) ? 'selected' : '' }} >{{ $reward->name }}</option>
                  @endforeach
                </select>
              </td>
              <td>
                <textarea name="achievement" class="ckeditor border-primary-500 border-1 p-2 w-full rounded-lg" required>
                  {{ $evaluation ? $evaluation->achievement : '' }}
                </textarea>
              </td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>
    </div>
    <div class="group-btn text-right mt-8">
      @if($evaluation)
        <a href="{{ route('evaluations.result') }}" class="btn btn-secondary ml-3">Xem đánh giá</a>
        <button {{$evaluation->status->name === 'Đã phê duyệt' ? 'disabled' : ''}} type="submit" class="btn btn-primary {{$evaluation->status->name === 'Đã phê duyệt' ? 'cursor-not-allowed bg-states-500/100 hover:bg-states-500/100' : ''}} ">Cập nhật</button>
      @else
        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
      @endif
    </div>
  </form>
@else
  @if ($evaluation)
  <div class="alert flex p-4 mb-5 bg-yellow-50 border-l-4 border-yellow-500 rounded">
    <span class="icomoon icon-exclamation-triangle text-yellow-500 mr-3 text-xl"></span>
    <div>
      <span class="font-medium">Thông báo:</span> Năm học này đã đóng đánh giá. Bạn không thể gửi đánh giá mới.
      @if ($evaluation)
      <a href="{{ route('evaluations.result') }}" class="text-states-500 hover:text-states-800 ml-3">Xem đánh giá</a>
      @endif
    </div>
  </div>
  @else
  <div class="alert flex p-4 mb-5 bg-blue-50 border-l-4 border-blue-500 rounded">
    <span class="icomoon icon-information-circle text-blue-500 mr-3 text-xl"></span>
    <div>
      <span class="font-medium">Thông báo:</span> Năm học này đã đóng đánh giá.
    </div>
  </div>
  @endif
@endif