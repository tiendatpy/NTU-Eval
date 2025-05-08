@extends('layouts.app')

@section('title', 'Danh sách đánh giá')

@section('content')
  <section class="unit-evaluations bg-white rounded-2xl py-8">
    <div class="container">
      <h2 class="mb-7 text-base">Danh Sách Đánh Giá Của Đơn Vị</h2>
      <table class="w-full text-sm overflow-hidden">
        <thead class="bg-states-300">
          <tr>
            <th class="w-5p">STT</th>
            <th class="w-20p">Họ tên</th>
            <th class="w-15p">Mức xếp loại</th>
            <th class="w-60p">Diễn giải</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($evaluations as $index => $evaluation)
            <tr class="bg-white">
              <td>{{ $index + 1 }}</td>
              <td>{{ $evaluation->evaluator->full_name }}</td>
              <td>{{ $evaluation->classification->name }}</td>
              <td>
                <ul>
                  @foreach ($evaluation->details->pluck('evidence')->filter() as $evidence)
                    <li class="list-disc">{{ $evidence }}</li>
                  @endforeach
                </ul>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">Không có đánh giá nào trong đơn vị.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection