{{-- filepath: e:\WORKSPACE\ntu-eval\resources\views\pages\unit-leader\last-report.blade.php --}}
@extends('layouts.app')

@section('title', 'Báo cáo tổng kết')

@section('content')
<section class="mod-last-report bg-white">
  <div class="container py-8">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between mb-6">
      <h2 class="text-base font-bold mb-0">Tờ trình tổng kết năm học</h2>
      <div class="flex items-center gap-3 mt-4 md:mt-0">
        <div class="flex items-center">
          <form method="get" action="{{ route('last-report') }}" class="flex items-center gap-3">
            <label for="year" class="text-sm font-medium text-gray-700">Năm học:</label>
            <select id="year" name="year" 
              class="border border-gray-300 text-gray-700 rounded-lg px-4 py-2.5 focus:ring-blue-500 focus:border-blue-500" 
              onchange="this.form.submit()">
              @foreach($years as $y)
                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }} - {{ $y + 1 }}</option>
              @endforeach
            </select>
          </form>
        </div>
        <a class="btn btn-primary flex items-center" href="{{ route('evaluations.export-last-report') }}?year={{ $selectedYear }}">
          <span>Xuất báo cáo</span>
        </a>
      </div>
    </div>

    <!-- Section 1 -->
    <div class=" rounded-lg mb-8 overflow-hidden shadow">
      <div class="bg-states-300  border-b border-blue-200 p-8">
        <h2 class="text-base font-semibold text-primary-800 mb-0">1. Danh sách tập thể, cá nhân đề nghị phê duyệt, xét công nhận danh hiệu thi đua</h2>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-primary-300 text-left">
              <th class=" w-16 font-semibold text-primary-900 rounded-tl-none">STT</th>
              <th class=" w-1/4 font-semibold text-primary-900">Tên cá nhân</th>
              <th class=" w-1/4 font-semibold text-primary-900">Danh hiệu</th>
              <th class=" font-semibold text-primary-900 rounded-none">Trích ngang thành tích</th>
            </tr>
          </thead>
          <tbody>
            @if ($evaluations->count() > 0)
            <!-- Category A: Individuals -->
              <tr>
                <td class=" font-bold ">A</td>
                <td class=" font-bold " colspan="3">Cá nhân</td>
              </tr>
              
              @foreach ($evaluations as $index => $evaluation)
                <tr class="border-t border-gray-200 ">
                  <td class=" text-gray-700">{{ $index + 1 }}</td>
                  <td class=" font-medium text-gray-800">{{ $evaluation->evaluator->full_name ?? 'N/A' }}</td>
                  <td class="">
                    @if($evaluation->approvedQuality || $evaluation->quality)
                      <div class="font-medium">{{ $evaluation->approvedQuality->name ?? $evaluation->quality->name }}</div>
                    @endif
                    @if($evaluation->approvedTitle || $evaluation->title)
                      <div class="mt-1">{{ $evaluation->approvedTitle->name ?? $evaluation->title->name }}</div>
                    @endif
                  </td>
                  <td class=" text-gray-700">{!! $evaluation->achievement !!}</td>
                </tr>
              @endforeach
                <!-- Category B: Unit -->
              <tr class=" border-t border-gray-200">
                <td class=" font-bold ">B</td>
                <td class=" font-bold ">Tập thể đơn vị</td>
                <td class="">
                  @if($unitEvaluation && $unitEvaluation->quality)
                    <div class="font-medium">{{ $unitEvaluation->quality->name }}</div>
                  @endif
                  @if($unitEvaluation && $unitEvaluation->title)
                    <div class="mt-1">{{ $unitEvaluation->title->name }}</div>
                  @endif
                </td>
                <td class=" text-gray-700">
                  @if($unitEvaluation)
                    {!! $unitEvaluation->achievement !!}
                  @else
                    <span class="text-gray-500 italic">Chưa có thông tin.</span>
                  @endif
                </td>
              </tr>
            @else
              <tr class="border-t border-gray-200">
                <td colspan="4" class="py-4 px-4 text-center text-gray-500 italic">Không có thông tin.</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    <!-- Section 2 -->
    <div class=" rounded-lg mb-8 overflow-hidden shadow">
      <div class="bg-states-300  border-b border-blue-200  p-8">
        <h2 class="text-base font-semibold text-primary-800 mb-0">2. Đề nghị khen thưởng</h2>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-primary-300 text-left">
              <th class=" w-16 font-semibold text-primary-800">STT</th>
              <th class=" w-1/4 font-semibold text-primary-800">Tên tập thể/cá nhân</th>
              <th class=" w-1/5 font-semibold text-primary-800">Hình thức khen thưởng</th>
              <th class=" font-semibold text-primary-800">Tóm tắt thành tích đạt được</th>
            </tr>
          </thead>
          <tbody>
            <!-- Category A: Individuals -->
            @if($evaluations->count() > 0)
              <tr class="">
                <td class=" font-bold">A</td>
                <td class=" font-bold" colspan="3">Cá nhân</td>
              </tr>
              @php $counter = 1; @endphp
              @foreach ($evaluations as $evaluation)
                @if($evaluation->reward && $evaluation->reward->name != 'Không')
                  <tr class="border-t border-gray-200 ">
                    <td class=" text-gray-700">{{ $counter++ }}</td>
                    <td class=" font-medium text-gray-800">{{ $evaluation->evaluator->full_name ?? 'N/A' }}</td>
                    <td class="">
                      <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ $evaluation->reward->name }}
                      </span>
                    </td>
                    <td class=" text-gray-700">{!! $evaluation->achievement !!}</td>
                  </tr>
                @endif
              @endforeach
              <!-- Category B: Unit -->
              @if($unitEvaluation && $unitEvaluation->reward && $unitEvaluation->reward->name != 'Không')
                <tr class=" border-t border-gray-200">
                  <td class=" font-bold">B</td>
                  <td class=" font-bold" colspan="3">Tập thể đơn vị</td>
                </tr>
                <tr class="border-t border-gray-200">
                  <td class=" text-gray-700">1</td>
                  <td class=" font-medium text-gray-800">{{ auth()->user()->unit->name ?? 'Đơn vị' }}</td>
                  <td class="">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-medium">
                      {{ $unitEvaluation->reward->name }}
                    </span>
                  </td>
                  <td class=" text-gray-700">{!! $unitEvaluation->achievement !!}</td>
                </tr>
              @endif
            @else
              <tr class="border-t border-gray-200">
                <td colspan="4" class="py-4 px-4 text-center text-gray-500 italic">Không có thông tin.</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-5 flex justify-end">
      {{ $evaluations->links() }}
    </div>
  </div>
</section>
@endsection
