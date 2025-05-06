@if ($evaluation)
  {{-- Hiển thị kết quả đánh giá --}}
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
          <td>{{ $detail->evidence }}</td>
          <td>{{ $detail->score }}</td>
        </tr>
      @endforeach
      <tr>
        <td colspan="4" class="text-right font-bold">
          Điểm đánh giá: {{ $evaluation->score }}
        </td>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td colspan="2" class="text-right">
          <span class="font-bold">Xếp loại chất lượng:</span>
          {{ $evaluation->classification->name }}
        </td>
      </tr>
    </tbody>
  </table>
@else
  @if ($isCurrentYear)
    {{-- Hiển thị form đánh giá nếu là năm hiện tại và chưa có đánh giá --}}
    <form action="{{ route('evaluations.store') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="self-table mb-5">
        <table class="w-full text-sm overflow-hidden">
          <thead class="rounded-t-xl">
            <tr class="bg-states-300">
              <th class="w-5p">STT</th>
              <th class="w-30p">Nội dung đánh giá</th>
              <th class="w-60p">Kê khai, minh chứng (nếu có)</th>
              <th class="w-5p">Mức đạt được (1-4)</th>
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
                  <div class="flex">
                    <input class="text-right w-50 score-input rounded-lg px-4 py-2" min="1" max="4" type="number" name="details[{{ $loop->index }}][score]" value="" required>
                  </div>
                </td>
              </tr>
            @endforeach
            <tr>
              <td colspan="4">
                <div class="text-right">
                  <span class="font-bold">Điểm đánh giá:</span>
                  <span class="total-score">0</span>
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
                      @foreach ($classifications as $classification)
                        <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
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