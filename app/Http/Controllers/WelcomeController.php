<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Periods;
use App\Models\Evaluation;
use App\Models\MetaType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Lấy ID của trạng thái "Mở" từ cache hoặc database
        $activeStatusId = Cache::remember('active_period_status_id', 86400, function () {
            return MetaType::where('category', 'period_status')
                  ->where('name', 'Mở')
                  ->value('id');
        });
        
        // Chỉ lấy đợt đánh giá có trạng thái "Mở"
        $activePeriod = null;
        if ($activeStatusId) {
            $activePeriod = Periods::where('status_id', $activeStatusId)
                ->orderBy('end_date', 'desc') // Ưu tiên đợt kết thúc gần nhất nếu có nhiều đợt mở
                ->with('status')
                ->first();
        }
            
        // Kiểm tra nếu người dùng đã có đánh giá trong kỳ này chưa
        $hasEvaluation = false;
        if ($activePeriod && $user) {
            $hasEvaluation = Evaluation::where('evaluator_id', $user->id)
                ->where('period_id', $activePeriod->id)
                ->exists();
        }
        
        // Tính số ngày còn lại (nếu có đợt đánh giá đang mở)
        $daysRemaining = null;
        $isOverdue = false;
        
        if ($activePeriod) {
            $today = now();
            if ($today->gt($activePeriod->end_date)) {
                $isOverdue = true;
                $daysRemaining = 0;
            } else {
                $daysRemaining = $today->diffInDays($activePeriod->end_date);
            }
        }
        
        return view('welcome', compact('activePeriod', 'hasEvaluation', 'daysRemaining', 'isOverdue'));
    }
}