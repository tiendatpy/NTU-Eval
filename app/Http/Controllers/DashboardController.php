<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Periods;
use App\Models\UnitEvaluation;
use App\Models\Quality;
use App\Models\Title;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Lấy tham số từ request
        $year = $request->input('year', now()->year - 1);
        $period = Periods::where('year', $year)->first();
        
        if (!$period) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }
        
        // Lấy danh sách năm để hiển thị dropdown
        $years = Periods::orderBy('year', 'desc')->pluck('year');
        
        // Lấy danh sách đánh giá của đơn vị
        $evaluations = Evaluation::with(['evaluator', 'quality', 'approvedQuality', 'title', 'approvedTitle'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id)
            ->get();
            
        // Tính số lượng theo từng loại xếp loại
        $qualityStats = $this->calculateQualityStats($evaluations);
        
        // Tính số lượng theo từng danh hiệu
        $titleStats = $this->calculateTitleStats($evaluations);
        
        // Lấy thông tin đánh giá của đơn vị
        $unitEvaluation = UnitEvaluation::with(['quality', 'title', 'reward'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id)
            ->first();
            
        return view('pages.unit-leader.statistics', compact(
            'years', 
            'year', 
            'evaluations', 
            'qualityStats', 
            'titleStats', 
            'unitEvaluation'
        ));
    }
    
    /**
     * Tính toán thống kê về xếp loại chất lượng
     */
    private function calculateQualityStats($evaluations)
    {
        $total = $evaluations->count();
        $stats = [];
        
        // Tổng hợp số lượng theo từng loại xếp loại
        $qualities = Quality::all();
        foreach ($qualities as $quality) {
            $count = $evaluations->filter(function ($evaluation) use ($quality) {
                return $evaluation->approved_quality_id 
                    ? $evaluation->approved_quality_id == $quality->id 
                    : $evaluation->quality_id == $quality->id;
            })->count();
            
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            
            $stats[$quality->name] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }
        
        return [
            'total' => $total,
            'items' => $stats
        ];
    }
    
    /**
     * Tính toán thống kê về danh hiệu thi đua
     */
    private function calculateTitleStats($evaluations)
    {
        $stats = [];
        
        // Tổng hợp số lượng theo từng danh hiệu
        $titles = Title::all();
        foreach ($titles as $title) {
            $count = $evaluations->filter(function ($evaluation) use ($title) {
                return $evaluation->approved_title_id 
                    ? $evaluation->approved_title_id == $title->id 
                    : $evaluation->title_id == $title->id;
            })->count();
            
            if ($count > 0) {
                $stats[$title->name] = $count;
            }
        }
        
        // Thêm thống kê cho hình thức khen thưởng
        $rewardStats = [];
        $rewards = Reward::all();
        foreach ($rewards as $reward) {
            if ($reward->name == 'Không') continue;
            
            $count = $evaluations->filter(function ($evaluation) use ($reward) {
                return $evaluation->reward_id == $reward->id;
            })->count();
            
            if ($count > 0) {
                $rewardStats[$reward->name] = $count;
            }
        }
        
        return [
            'titles' => $stats,
            'rewards' => $rewardStats
        ];
    }
}