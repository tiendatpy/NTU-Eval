@extends('layouts.app')

@section('title', 'Trang Chủ')

@section('content')
    <section class="mod-self-eval bg-white rounded-2xl py-8">
      <div class="container">
        <div class="self-eval-heading">
          <h2>Tự đánh giá</h2>
          <p>Đây là bảng tự đánh giá</p>
        </div>
        <div class="self-table">
          <table class="w-full text-sm overflow-hidden">
            <thead class="rounded-t-xl">
              <tr class="text-primary-500 bg-primary-050">
                <th>STT</th>
                <th>Nội dung đánh giá</th>
                <th>Kê khai minh chứng (nếu có)</th>
                <th>Mức đạt được</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($evaluations as $eval)
                <tr class="bg-white">
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $eval->id }}</td>
                  <td><input type="file"></td>
                  <td>
                    <select name="" id="">
                      <option value="">Chọn mức đạt được</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                    </select>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </section>
@endsection