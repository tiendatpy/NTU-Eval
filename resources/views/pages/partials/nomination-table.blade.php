@if ($nominations->isNotEmpty())
  {{-- Hiển thị kết quả đề xuất --}}
  <table class="w-full text-sm overflow-hidden">
    <thead class="rounded-t-xl">
      <tr class="bg-states-300">
        <th class="w-20p">Danh hiệu</th>
        <th class="w-20p">Hình thức khen thưởng</th>
        <th class="w-50p">Tóm tắt thành tích</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($nominations as $nomination)
        <tr class="bg-white">
          <td>{{ $nomination->title->name }}</td>
          <td>{{ $nomination->reward->name }}</td>
          <td>{{ $nomination->achievement }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@else
  @if ($isCurrentYear)
    {{-- Hiển thị form đề xuất nếu là năm hiện tại và chưa có đề xuất --}}
    <form action="{{ route('title-nominations.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="self-table mb-5">
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
      <div class="group-btn text-right mt-8">
        <button type="submit" class="btn btn-primary">Gửi đề xuất</button>
      </div>
    </form>
  @else
    {{-- Hiển thị thông báo nếu là năm khác và chưa có đề xuất --}}
    <p>Chưa có dữ liệu đề xuất cho năm này.</p>
  @endif
@endif