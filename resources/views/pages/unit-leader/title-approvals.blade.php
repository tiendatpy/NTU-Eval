@extends('layouts.app')

@section('title', 'Xét Duyệt Danh Hiệu Thi Đua')

@section('content')
  <section class="unit-leader-approvals bg-white">
    <div class="container py-8">
      <h2 class="mb-7 text-base">Danh Sách Xét Duyệt Danh Hiệu Thi Đua</h2>
      <table class="w-full text-sm overflow-hidden">
        <thead class="bg-states-300">
          <tr>
            <th class="w-5p">STT</th>
            <th class="w-15p">Tên cá nhân</th>
            <th class="w-10p">Danh Hiệu</th>
            <th class="w-15p">Hình Thức <br> Khen Thưởng</th>
            <th class="w-25p">Tóm Tắt Thành Tích</th>
            <th class="w-20p">Góp Ý</th>
            <th class="w-10p">Duyệt</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($nominations as $index => $nomination)
            <tr class="bg-white">
              <td>{{ $index + 1 }}</td>
              <td>
                <a href="{{ route('unit-leader-approvals.show', $nomination->id) }}" class="hover:text-states-400" title="Xem chi tiết">
                  {{ $nomination->evaluator->full_name }}
                </a>
              </td>
              <td>{{ $nomination->title->name }}</td>
              <td>{{ $nomination->reward->name }}</td>
              <td>{{ $nomination->achievement }}</td>
              <td>
                {{ $nomination->review}}
              </td>
              <td>
                <button class="confirm-title-nomination" data-id="{{ $nomination->id }}" title="Duyệt danh hiệu">
                  <span class="icomoon icon-check-circle text-3xl text-neutral-500"></span>
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Phân trang -->
      <div class="mt-4">
        {{ $nominations->links() }}
      </div>
    </div>
  </section>
@endsection