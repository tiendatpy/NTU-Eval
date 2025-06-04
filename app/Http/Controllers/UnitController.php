<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Periods;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function members(Request $request)
    {
        $user = auth()->user();
        $isUnitLeader = $user->role->isUnitLeader == true;

        if (!$isUnitLeader) {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập tính năng này.');
        }

        $currentYear = now()->year - 1;

        $periodId = Periods::where('year', $currentYear)->value('id');

        if (!$periodId) {
            $periodId = Periods::orderBy('year', 'desc')->value('id');
            $currentYear = Periods::where('id', $periodId)->value('year');
        }

        // Lấy danh sách ID người dùng đã đánh giá trong kỳ này - không bị ảnh hưởng bởi bộ lọc
        $evaluatedUserIds = [];
        if ($periodId) {
            $evaluatedUserIds = Evaluation::where('period_id', $periodId)
                ->where('unit_id', $user->unit_id)
                ->pluck('evaluator_id')
                ->toArray();
        }

        // Tính toán thống kê tổng thể - không bị ảnh hưởng bởi bộ lọc
        $allMembers = User::where('unit_id', $user->unit_id)->get();
        $totalMembers = $allMembers->count();
        $evaluatedCount = count($evaluatedUserIds);
        $notEvaluatedCount = $totalMembers - $evaluatedCount;
        $evaluationPercentage = $totalMembers > 0 ? round(($evaluatedCount / $totalMembers) * 100) : 0;

        // Lấy tham số tìm kiếm từ request
        $searchName = $request->input('search_name');
        $evaluationStatus = $request->input('evaluation_status');

        // Lấy danh sách thành viên có thể áp dụng bộ lọc
        $membersQuery = User::where('unit_id', $user->unit_id)
            ->with('role');
            
        // Lọc theo họ tên nếu có
        if ($searchName) {
            $membersQuery->where('full_name', 'like', '%' . $searchName . '%');
        }

        // Lọc theo trạng thái đánh giá nếu có
        if ($evaluationStatus) {
            if ($evaluationStatus == 'evaluated') {
                $membersQuery->whereIn('id', $evaluatedUserIds);
            } elseif ($evaluationStatus == 'not_evaluated') {
                $membersQuery->whereNotIn('id', $evaluatedUserIds);
            }
        }

        // Sắp xếp và phân trang
        $members = $membersQuery->orderBy('full_name')
            ->paginate(10)
            ->withQueryString(); // Thêm withQueryString để giữ các tham số tìm kiếm trong URL phân trang

        // Thêm số lượng kết quả lọc (nếu có áp dụng bộ lọc)
        $filteredCount = $members->total();

        return view('pages.unit-leader.unit-members', compact(
            'members', 
            'currentYear', 
            'evaluatedUserIds', 
            'totalMembers', 
            'evaluatedCount', 
            'notEvaluatedCount',
            'evaluationPercentage',
            'searchName',
            'evaluationStatus',
            'filteredCount'
        ));
    }
}
