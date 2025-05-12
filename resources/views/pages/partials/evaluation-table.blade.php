@if ($evaluation)
  {{-- Hiển thị kết quả đánh giá --}}
  <div class="self-table mb-5">
    <div class="self-evaluation mb-5">
      <h3 class="mb-5">I. TỰ ĐÁNH GIÁ</h3>
      <table class="w-full text-sm overflow-hidden">
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
            <td colspan="4">
              <div class="text-right">
                <span class="font-bold">Điểm đánh giá:</span>
                <span>{{ $evaluation->rating }}</span>
              </div>
            </td>
          </tr>
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
      <div ">
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
    
    @if ($evaluation->review)
    <div class="self-review mb-5">
      <h3 class="mb-5">IV. BÌNH XÉT CỦA TRƯỞNG ĐƠN VỊ</h3>
      <div class="border-primary-500 border-1 p-3 rounded-lg bg-white">
        {!! $evaluation->review !!}
      </div>
    </div>
    @endif

    @if ($evaluation->approved_quality_id)
    <div class="approved-quality mb-5">
      <h3 class="mb-5">V. XẾP LOẠI ĐƯỢC PHÊ DUYỆT</h3>
      <div class="border-primary-500 border-1 p-3 rounded-lg bg-white font-medium">
        {{ $evaluation->approvedQuality->name }}
      </div>
    </div>
    @endif

    @if ($evaluation->approved_title_id)
    <div class="approved-title-nomination mb-5">
      <h3 class="mb-5">VI. DANH HIỆU ĐƯỢC PHÊ DUYỆT</h3>
      <div class="border-primary-500 border-1 p-3 rounded-lg bg-white font-medium">
        {{ $evaluation->approvedTitle->name }}
      </div>
    </div>
    @endif
  </div>
@else
  @if ($isCurrentYear)
    {{-- Hiển thị form đánh giá nếu là năm hiện tại và chưa có đánh giá --}}
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
                <tr class="bg-white">
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <input type="hidden" name="details[{{ $loop->index }}][criteria_id]" value="{{ $cri->id }}">
                    {{ $cri->name }}
                  </td>
                  <td>
                    <textarea class="ckeditor" name="details[{{ $loop->index }}][evidence]" 
                      class="border-primary-500 border-1 p-2 w-full rounded-lg" 
                      placeholder="Nhập thông tin minh chứng (nếu có)">
                    </textarea>
                  </td>
                  <td>
                    <select name="details[{{ $loop->index }}][rating]" class="border-primary-500 border-1 p-2 rounded-lg w-50 rating" required>
                      <option value="4">Xuất sắc</option>
                      <option value="3">Tốt</option>
                      <option value="2">Trung bình</option>
                      <option value="1">Yếu</option>
                    </select>
                  </td>
                </tr>
              @endforeach
              <tr>
                <td colspan="4">
                  <div class="text-right">
                    <span class="font-bold">Điểm đánh giá:</span>
                    <span class="total-score">4</span>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2"></td>
                <td colspan="2">
                  <div class="flex justify-between">
                    <span class="inline-block w-2/3 font-bold">Tự xếp loại chất lượng:</span>
                    <div class="w-1/3">
                      <select name="classification_id" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                        @foreach ($quality as $qual)
                          <option value="{{ $qual->id }}">{{ $qual->name }}</option>
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
            <textarea name="comment" id="comment" class="ckeditor"></textarea>
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
                  <select name="title_id" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                    @foreach ($titles as $title)
                      <option value="{{ $title->id }}">{{ $title->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <select name="reward_id" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                    @foreach ($rewards as $reward)
                      <option value="{{ $reward->id }}">{{ $reward->name }}</option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <textarea name="achievement" class="ckeditor border-primary-500 border-1 p-2 w-full rounded-lg" required></textarea>
                </td>
              </tr>
            </tbody>
          </table>
         </div>
       </div>
      </div>
      <div class="group-btn text-right mt-8">
        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
      </div>
    </form>
  @else
    {{-- Hiển thị thông báo nếu là năm khác và chưa có đánh giá --}}
    <p>Không có dữ liệu đánh giá cho năm này.</p>
  @endif
@endif