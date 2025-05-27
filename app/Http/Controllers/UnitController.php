<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Periods;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function members()
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

        $members = User::where('unit_id', $user->unit_id)
            ->with('role')
            ->orderBy('full_name')
            ->paginate(10);


        $evaluatedUserIds = [];
        if ($periodId) {
            $evaluatedUserIds = Evaluation::where('period_id', $periodId)
                ->where('unit_id', $user->unit_id)
                ->pluck('evaluator_id')
                ->toArray();
        }

        // Thêm thống kê
        $totalMembers = $members->total();
        $evaluatedCount = count($evaluatedUserIds);
        $notEvaluatedCount = $totalMembers - $evaluatedCount;
        $evaluationPercentage = $totalMembers > 0 ? round(($evaluatedCount / $totalMembers) * 100) : 0;

        return view('pages.unit-leader.unit-members', compact(
            'members', 
            'currentYear', 
            'evaluatedUserIds', 
            'totalMembers', 
            'evaluatedCount', 
            'notEvaluatedCount',
            'evaluationPercentage'
        ));
    }
}
