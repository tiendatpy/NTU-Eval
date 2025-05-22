<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitEvaluation;
use App\Models\Unit;
use App\Models\Periods;
use App\Models\Quality;
use App\Models\Title;
use App\Models\Reward;
use App\Models\User;
use App\Models\MetaType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Exception;

class UnitEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Kiểm tra vai trò của người dùng - chỉ trưởng đơn vị mới được đánh giá đơn vị
        $isUnitLeader = $user->role->name === 'Trưởng đơn vị';
        if (!$isUnitLeader) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập chức năng này.');
        }

        $selectedYear = $request->input('year', now()->year - 1);

        // Tìm kỳ đánh giá theo năm
        $currentPeriod = Periods::where('year', $selectedYear)->first();
        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }

        // Tìm đánh giá của đơn vị cho kỳ đánh giá đó
        $unitEvaluation = UnitEvaluation::where('unit_id', $user->unit_id)
            ->where('period_id', $currentPeriod->id)
            ->first();

        // Lấy dữ liệu cần thiết cho form
        $periods = Periods::orderBy('year', 'desc')->get();
        $quality = Quality::all();

        $titleTypeId = Cache::remember('title_type_unit_id', 86400, function () {
            return MetaType::where('category', 'type_title')
                ->where('name', 'Tập thể')
                ->value('id');
        });
        
        // Nếu không tìm thấy loại danh hiệu "Tập thể", sử dụng tất cả danh hiệu
        if ($titleTypeId) {
            $titles = Title::where('type_id', $titleTypeId)->get();
        } else {
            $titles = Title::all();
        }

        $rewards = Reward::all();
        $isCurrentYear = $selectedYear == now()->year - 1;

        if ($request->ajax()) {
            // Trả về HTML của form đánh giá qua AJAX
            $html = view('pages.partials.unit-evaluation-form', compact(
                'unitEvaluation',
                'isCurrentYear',
                'quality',
                'titles',
                'rewards'
            ))->render();

            return response()->json(['html' => $html]);
        }

        return view('pages.unit-evaluation', compact(
            'unitEvaluation',
            'periods',
            'quality',
            'titles',
            'rewards',
            'selectedYear',
            'isCurrentYear'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Kiểm tra vai trò
        $isUnitLeader = $user->role->name === 'Trưởng đơn vị';
        if (!$isUnitLeader) {
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền thực hiện chức năng này.');
        }

        // Lấy period_id từ kỳ đánh giá của năm hiện tại - 1
        $currentPeriod = Periods::where('year', now()->year - 1)->first();
        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm hiện tại.');
        }
        $periodId = $currentPeriod->id;

        // Validate dữ liệu đầu vào
        $request->validate([
            'quality_id' => 'required|exists:quality,id',
            'title_id' => 'required|exists:titles,id',
            'reward_id' => 'required|exists:rewards,id',
            'achievement' => 'required|string',
            'evidence' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Kiểm tra nếu đã có đánh giá cho đơn vị trong kỳ này
            $existingEvaluation = UnitEvaluation::where('unit_id', $user->unit_id)
                ->where('period_id', $periodId)
                ->first();

            if ($existingEvaluation) {
                // Cập nhật đánh giá hiện có
                $existingEvaluation->update([
                    'quality_id' => $request->quality_id,
                    'title_id' => $request->title_id,
                    'reward_id' => $request->reward_id,
                    'evidence' =>$request->evidence,
                    'achievement' =>$request->achievement,
                ]);
                
                $message = 'Cập nhật đánh giá đơn vị thành công.';
            } else {
                // Tạo đánh giá mới
                UnitEvaluation::create([
                    'unit_id' => $user->unit_id,
                    'period_id' => $periodId,
                    'quality_id' => $request->quality_id,
                    'title_id' => $request->title_id,
                    'reward_id' => $request->reward_id,
                    'evidence' =>$request->evidence,
                    'achievement' =>$request->achievement,
                ]);
                
                $message = 'Tự đánh giá đơn vị thành công.';
            }

            DB::commit();
            return redirect()->route('unit.evaluations.index')->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // public function getListUnitEvaluations(Request $request)
    // {
    //     $user = auth()->user();
    //     if (!$user) {
    //         return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
    //     }

    //     // Lấy danh sách các năm để lọc
    //     $years = Periods::orderBy('year', 'desc')->pluck('year')->unique();

    //     // Lấy năm được chọn, mặc định là năm hiện tại - 1
    //     $defaultYear = now()->year - 1;

    //     // Kiểm tra xem năm mặc định có trong danh sách năm không
    //     if (!$years->contains($defaultYear)) {
    //         $defaultYear = $years->first();
    //     }

    //     $selectedYear = $request->input('year', $defaultYear);

    //     // Lấy period_id từ năm được chọn
    //     $period = Periods::where('year', $selectedYear)->first();

    //     if (!$period) {
    //         return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
    //     }

    //     // Xây dựng query để lấy dữ liệu
    //     $query = UnitEvaluation::where('period_id', $period->id)
    //         ->with(['unit', 'quality', 'title', 'reward']);
            
    //     // Nếu là quản trị viên hoặc manager cao cấp, có thể xem tất cả đơn vị
    //     // Nếu là trưởng đơn vị, chỉ xem được đơn vị của mình
    //     $isAdmin = $user->role->name === 'Quản trị viên';
    //     $isManager = $user->role->name === 'Ban giám hiệu';
        
    //     if (!$isAdmin && !$isManager) {
    //         $query->where('unit_id', $user->unit_id);
    //     }

    //     $evaluations = $query->orderBy('unit_id', 'asc')->get();

    //     return view('pages.all-unit-evaluations', compact(
    //         'evaluations',
    //         'years',
    //         'selectedYear',
    //         'isAdmin',
    //         'isManager'
    //     ));
    // }



}