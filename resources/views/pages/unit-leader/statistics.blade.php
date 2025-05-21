@extends('layouts.app')

@section('title', 'Thống kê đánh giá')

@section('content')
<div class="stats-dashboard py-8">
  <div class="container px-4">
    <!-- Header & Filter -->
    <div class="bg-white rounded-xl custom-box-shadow p-6 mb-8">
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
          <h2 class="text-xl font-bold text-primary-800">Thống kê đánh giá năm học {{ $year }} - {{ $year + 1 }}</h2>
          <p class="text-gray-500 mt-1">Đơn vị: {{ auth()->user()->unit->name }}</p>
        </div>
        
        <!-- Filter năm học -->
        <form method="get" action="{{ route('dashboard.stats') }}" class="flex items-center gap-3">
          <label for="year" class="text-sm font-medium text-gray-700">Năm học:</label>
          <select id="year" name="year" 
            class="border border-gray-300 text-gray-700 rounded-lg px-4 py-2.5 focus:ring-blue-500 focus:border-blue-500" 
            onchange="this.form.submit()">
            @foreach($years as $y)
              <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }} - {{ $y + 1 }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </div>
    
    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl custom-box-shadow p-6 text-white">
        <div class="flex items-center justify-between h-full">
          <div>
            <h3 class="text-lg text-primary-800">Tổng viên chức</h3>
            <p class="text-3xl font-bold mt-2">{{ $qualityStats['total'] }}</p>
          </div>
          <div class="text-white text-opacity-80">
            <span class="icomoon icon-users text-2xl"></span>
          </div>
        </div>
      </div>

      @php
        $htxsnvCount = $qualityStats['items']['Hoàn thành xuất sắc']['count'] ?? 0;
        $httnvCount = $qualityStats['items']['Hoàn thành tốt']['count'] ?? 0;
        $htnvCount = $qualityStats['items']['Hoàn thành']['count'] ?? 0;
      @endphp
      
      <div class="stat-card bg-gradient-to-br from-green-500 to-green-700 rounded-xl custom-box-shadow p-6 text-white">
        <div class="flex items-center justify-between h-full">
          <div>
            <h3 class="text-lg text-primary-800">HTXSNV</h3>
            <p class="text-3xl font-bold mt-2">{{ $htxsnvCount }}</p>
            <p class="text-sm mt-1">{{ $qualityStats['items']['Hoàn thành xuất sắc']['percentage'] ?? 0 }}% tổng số</p>
          </div>
          <div class="text-white text-opacity-80">
            <span class="icomoon icon-check-circle text-2xl"></span>
          </div>
        </div>
      </div>
      
      <div class="stat-card bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl custom-box-shadow p-6 text-white">
        <div class="flex items-center justify-between h-full">
          <div>
            <h3 class="text-lg text-primary-800">HTTNV</h3>
            <p class="text-3xl font-bold mt-2">{{ $httnvCount }}</p>
            <p class="text-sm mt-1">{{ $qualityStats['items']['Hoàn thành tốt']['percentage'] ?? 0 }}% tổng số</p>
          </div>
          <div class="text-white text-opacity-80">
            <span class="icomoon icon-star text-2xl"></span>
          </div>
        </div>
      </div>
      
      <div class="stat-card bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl custom-box-shadow p-6 text-white">
        <div class="flex items-center justify-between h-full">
          <div>
            <h3 class="text-lg text-primary-800">HTNV</h3>
            <p class="text-3xl font-bold mt-2">{{ $htnvCount }}</p>
            <p class="text-sm mt-1">{{ $qualityStats['items']['Hoàn thành']['percentage'] ?? 0 }}% tổng số</p>
          </div>
          <div class="text-white text-opacity-80">
            <span class="icomoon icon-chart-bar text-2xl"></span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Biểu đồ phân bố xếp loại -->
      <div class="bg-white rounded-xl custom-box-shadow p-6">
        <h3 class="text-lg text-primary-800 font-bold mb-4">Phân bố xếp loại chất lượng</h3>
        <div class="chart-container">
          <canvas id="qualityChart" data-quality="{{ json_encode([
            'labels' => collect($qualityStats['items'])->filter(function($data) { return $data['count'] > 0; })->keys()->toArray(),
            'data' => collect($qualityStats['items'])->filter(function($data) { return $data['count'] > 0; })->pluck('count')->toArray()
          ]) }}"></canvas>
        </div>
      </div>
      
      <!-- Biểu đồ danh hiệu thi đua -->
      <div class="bg-white rounded-xl custom-box-shadow p-6">
        <h3 class="text-lg text-primary-800 font-bold mb-4">Danh hiệu thi đua</h3>
        <div class="chart-container">
          <canvas id="titleChart" data-title="{{ json_encode([
            'labels' => array_keys($titleStats['titles']),
            'data' => array_values($titleStats['titles'])
          ]) }}"></canvas>
        </div>
      </div>
    </div>
    
    <!-- Detailed Stats Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Chi tiết xếp loại -->
      <div class="bg-white rounded-xl custom-box-shadow p-6">
        <h3 class="text-lg text-primary-800 font-bold mb-4">Chi tiết xếp loại chất lượng</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Xếp loại</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tỷ lệ</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($qualityStats['items'] as $quality => $data)
                @if($data['count'] > 0)
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $quality }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['count'] }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['percentage'] }}%</td>
                </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Chi tiết danh hiệu -->
      <div class="bg-white rounded-xl custom-box-shadow p-6">
        <h3 class="text-lg text-primary-800 font-bold mb-4">Chi tiết danh hiệu thi đua</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh hiệu</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($titleStats['titles'] as $title => $count)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $title }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $count }}</td>
              </tr>
              @endforeach
              @if($unitEvaluation)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $unitEvaluation->title->name }} (Tập thể)</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1</td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
        
        <h3 class="text-lg text-primary-800 font-bold mt-6 mb-4">Chi tiết khen thưởng</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hình thức khen thưởng</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($titleStats['rewards'] as $reward => $count)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reward }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $count }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <!-- Export Buttons -->
    <div class="flex justify-end gap-4 mt-8">
      <a href="{{ route('evaluations.export-quality-ratings', ['year' => $year]) }}" 
        class="btn btn-primary">
        <span class="icomoon icon-download-v2"></span>
        <span>Xuất báo cáo tổng kết</span>
      </a>
    </div>
  </div>
</div>

@endsection