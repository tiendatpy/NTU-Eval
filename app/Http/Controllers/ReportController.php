<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationCriteria;
use App\Models\MetaType;
use App\Models\Periods;
use App\Models\Quality;
use App\Models\Title;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\UnitEvaluation;

class ReportController extends Controller
{
  public function getListForUnitReport(Request $request)
  {
    $user = auth()->user();

    // Lấy danh sách các năm để lọc
    $years = Periods::orderBy('year', 'desc')->pluck('year')->unique();

    // Lấy năm được chọn, mặc định là năm hiện tại - 1
    $defaultYear = now()->year - 1;

    // Kiểm tra xem năm mặc định có trong danh sách năm không
    if (!$years->contains($defaultYear)) {
      $defaultYear = $years->first(); // Nếu không có, lấy năm gần nhất
    }

    $selectedYear = $request->input('year', $defaultYear);

    // Lấy period_id từ năm được chọn
    $period = Periods::where('year', $selectedYear)->first();

    $quality = Quality::all();

    if (!$period) {
      return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
    }

    // Xây dựng query cơ bản
    $query = Evaluation::where('period_id', $period->id)
      ->with(['evaluator', 'quality', 'title', 'reward', 'details']);
    
    $unitEvaluation = UnitEvaluation::where('unit_id', $user->unit_id)
      ->where('period_id', $period->id)
      ->with(['quality', 'title', 'reward'])
      ->first();

    $evaluations = $query->orderBy('created_at', 'asc')->paginate(3);

    return view('pages.unit-leader.unit-report', compact(
      'evaluations',
      'years',
      'quality',
      'selectedYear',
      'unitEvaluation'
    ));
  }
  public function getListForLastReport(Request $request)
  {
    $user = auth()->user();
    if (!$user) {
      return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
    }

    $years = Periods::orderBy('year', 'desc')->pluck('year')->unique();

    $defaultYear = now()->year - 1;

    if (!$years->contains($defaultYear)) {
      $defaultYear = $years->first(); // Nếu không có, lấy năm gần nhất
    }

    $selectedYear = $request->input('year', $defaultYear);

    $period = Periods::where('year', $selectedYear)->first();

    if (!$period) {
      return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
    }

    // Lấy danh sách các title để hiển thị trong dropdown
    $titleTypeId = Cache::remember('title_type_id', 86400, function () {
      return MetaType::where('category', 'type_title')
        ->where('name', 'Cá nhân')
        ->value('id');
    });
    $titles = Title::where('type_id', $titleTypeId)->get();

    $query = Evaluation::with(['evaluator', 'title', 'reward', 'status'])
      ->where('period_id', $period->id)
      ->where('unit_id', $user->unit_id);

    $evaluations = $query->orderBy('created_at', 'asc')
      ->paginate(5);

      return view('pages.unit-leader.last-report', compact('evaluations', 'years', 'selectedYear', 'titles'));
  }
}
