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
          <select class="border-primary-500 border-1 p-2 w-100 rounded-lg" id="period_id" name="period_id" required>
            @foreach ($periods as $period)
            <option value="{{ $period->id }}"{{ $period->year == now()->year - 1 ? 'selected' : '' }}>
              {{ $period->year }} - {{ $period->year + 1 }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="self-table mb-5">
          <table class="w-full text-sm overflow-hidden">
          <thead class="rounded-t-xl">
            <tr class=" bg-states-300">
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
                <input  class="text-right w-50 score-input rounded-lg px-4 py-2" min="1" max="4" type="number" name="details[{{ $loop->index }}][score]" value="" required>
              </div>
            </td>
            </tr>
            @endforeach
            <tr>
              <td colspan="4">
                <div class="text-right">
                  <span class="font-bold">Điểm đánh giá:</span>
                  <span class="total-score" >0</span>
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
          {{-- <button type="reset" class="btn btn-secondary">Lưu đánh giá</button> --}}
          <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
        </div>
      </div>
    </section>
  </form>
@endsection