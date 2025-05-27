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
        $isUnitLeader = $user->role->isUnitLeader == true;
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
        $isUnitLeader = $user->role->isUnitLeader == true;
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
                    'evidence' => $request->evidence,
                    'achievement' => $request->achievement,
                ]);

                $message = 'Cập nhật đánh giá đơn vị thành công.';
            } else {
                // Tạo đánh giá mới
                UnitEvaluation::create([
                    'evaluator_id' => $user->id,
                    'unit_id' => $user->unit_id,
                    'period_id' => $periodId,
                    'quality_id' => $request->quality_id,
                    'title_id' => $request->title_id,
                    'reward_id' => $request->reward_id,
                    'evidence' => $request->evidence,
                    'achievement' => $request->achievement,
                    
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

    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $unitEvaluation = UnitEvaluation::findOrFail($id);
        
        // Kiểm tra quyền phê duyệt
        if (!$user->role->canApproveEvaluations && !$user->role->isUnitLeader && !$user->role->isSuperAdmin) {
            return redirect()->back()->with('error', 'Bạn không có quyền phê duyệt đánh giá này.');
        }
        
        // Kiểm tra xem có phải đánh giá của đơn vị mình không
        if ($unitEvaluation->unit_id != $user->unit_id && !$user->role->isSuperAdmin) {
            return redirect()->back()->with('error', 'Bạn không có quyền phê duyệt đánh giá đơn vị khác.');
        }
        
        // Kiểm tra xem đánh giá đã được phê duyệt chưa
        if ($unitEvaluation->approved_quality_id) {
            return redirect()->back()->with('error', 'Đánh giá này đã được phê duyệt.');
        }
        
        // Validate dữ liệu
        $validated = $request->validate([
            'approved_quality_id' => 'required|exists:quality,id',
            'approved_title_id' => 'required|exists:titles,id',
            'approved_reward_id' => 'required|exists:rewards,id',
            'approved_achievement' => 'required|string',
            'approved_evidence' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Cập nhật thông tin phê duyệt
            $unitEvaluation->update([
                'approved_quality_id' => $request->approved_quality_id,
                'approved_title_id' => $request->approved_title_id,
                'approved_reward_id' => $request->approved_reward_id,
                'approved_achievement' => $request->approved_achievement,
                'approved_evidence' => $request->approved_evidence,
                'is_approved' => true,
                'approved_by' => $user->id
            ]);
            
            DB::commit();
            
            return redirect()->route('unit-evaluations.approve')->with('success', 'Đánh giá đơn vị đã được phê duyệt thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi trong quá trình phê duyệt: ' . $e->getMessage());
        }
    }

    public function approvalList(Request $request)
    {
        $user = Auth::user();
        
        // Lấy năm được chọn từ request hoặc mặc định là năm hiện tại - 1
        $selectedYear = $request->input('year', now()->year - 1);
        
        // Tìm kỳ đánh giá theo năm
        $currentPeriod = Periods::where('year', $selectedYear)->first();
        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }
        
        // Tìm đánh giá của đơn vị cho kỳ đánh giá đó
        $unitEvaluation = UnitEvaluation::where('unit_id', $user->unit_id)
            ->where('period_id', $currentPeriod->id)
            ->with(['quality', 'title', 'reward', 'approvedQuality', 'approvedTitle', 'approvedReward', 'unit', 'evaluator', 'approver'])
            ->first();
        
        if (!$unitEvaluation) {
            return view('pages.unit-leader.approved-unit-evaluation', [
                'unitEvaluation' => null,
                'selectedYear' => $selectedYear,
                'years' => Periods::orderBy('year', 'desc')->pluck('year')->unique(),
            ])->with('info', 'Không tìm thấy đánh giá đơn vị cho năm học đã chọn.');
        }
        
        // Lấy danh sách xếp loại chất lượng
        $qualities = Quality::all();
        
        // Lấy danh sách danh hiệu thi đua cho đơn vị
        $titleTypeId = MetaType::where('category', 'type_title')
            ->where('name', 'Tập thể')
            ->value('id');
        
        if ($titleTypeId) {
            $titles = Title::where('type_id', $titleTypeId)->get();
        } else {
            $titles = Title::all();
        }
        
        // Lấy danh sách khen thưởng
        $rewards = Reward::all();
        
        // Lấy danh sách các năm
        $years = Periods::orderBy('year', 'desc')->pluck('year')->unique();
        
        return view('pages.unit-leader.approved-unit-evaluation', compact(
            'unitEvaluation',
            'qualities',
            'titles',
            'rewards',
            'selectedYear',
            'years'
        ));
    }
}
