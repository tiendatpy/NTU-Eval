@extends('layouts.app')

@section('title', 'Tự Đánh Giá')

@section('content')
  <form action="{{ route('evaluations.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <section class="mod-self-eval bg-white rounded-2xl py-8">
      <div class="container">
        <div class="self-eval-heading mb-8 text-sm">
          <h2 class="mb-7 text-base">Tự đánh giá</h2>
          <label for="period">Năm đánh giá</label>
          <select class="border-primary-500 border-1 p-2 w-100 rounded-lg" id="period" name="period" required>
            @for ($year = 2022; $year <= now()->year; $year++)
              <option value="{{ $year }}" {{ $evaluation->period == $year ? 'selected' : '' }}>
                {{ $year }}-{{ $year + 1 }}
              </option>
            @endfor
          </select>
        </div>
        <div class="self-table mb-5">
          <table class="w-full text-sm overflow-hidden">
          <thead class="rounded-t-xl">
            <tr class=" bg-states-300">
            <th class="w-5p">STT</th>
            <th class="w-50p">Nội dung đánh giá</th>
            <th class="w-20p">Kê khai, minh chứng (nếu có)</th>
            <th class="w-25p">Mức đạt được (1-4)</th>
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
              <input type="file" name="details[{{ $loop->index }}][evidence]" accept=".jpg,.jpeg,.png,.pdf,.docx,.doc,.xls,.xlsx">
            </td>
            <td>
              <div class="flex">
                <input  class="text-right w-50 score-input rounded-lg px-4 py-2" min="1" max="4" type="number" name="details[{{ $loop->index }}][score]" value="" required>
              </div>
            </td>
            </tr>
            @endforeach
            <tr>
              <td colspan="3"></td>
              <td>
                <span class="font-bold">Điểm đánh giá: </span>
                <span class="total-score" >0</span>
              </td>
            </tr>
            <tr>
              <td colspan="2"></td>
              <td>
                <span class="font-bold">Tự xếp loại chất lượng:</span>
              </td>
              <td>
                <select name="classification_id" class="border-primary-500 border-1 p-2 w-full rounded-lg" required>
                  @foreach ($classifications as $classification)
                    <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                  @endforeach
                </select>
              </td>
            </tr>
          </tbody>
          </table>
        </div>
        <div class="group-btn text-right mt-8">
          {{-- <button type="reset" class="btn btn-secondary">Lưu đánh giá</button> --}}
          <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
        </div>
      </div>
    </section>
  </form>
@endsection