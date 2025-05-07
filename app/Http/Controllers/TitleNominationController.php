<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\TitleNomination;
use App\Models\MetaType;
use App\Models\Periods;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TitleNominationController extends Controller
{
  public function index(Request $request)
  {
    // Lấy năm được chọn từ request, mặc định là năm hiện tại
    $selectedYear = $request->input('year', now()->year-1);

    // Tìm kỳ đánh giá theo năm
    $selectedPeriod = Periods::where('year', $selectedYear)->first();

    // Lấy danh sách đề xuất danh hiệu cho kỳ đánh giá
    $nominations = TitleNomination::where('period_id', $selectedPeriod->id ?? null)
        ->where('user_id', Auth::id())
        ->get();

    // Lấy danh sách danh hiệu và phần thưởng
    $titles = Title::all();
    $rewards = Reward::all();
    $periods = Periods::all();

    $isCurrentYear = $selectedYear == now()->year-1;

    if ($request->ajax()) {
        $html = view('pages.partials.nomination-table', compact('titles', 'rewards', 'nominations', 'isCurrentYear'))->render();
        return response()->json(['html' => $html]);
    }

    return view('pages.title-nominations', compact('titles', 'rewards', 'periods', 'nominations', 'isCurrentYear', 'selectedYear'));
  }

  public function store(Request $request)
  {
    // Lấy năm từ request hoặc mặc định là năm hiện tại
    $year = $request->input('year', now()->year-1);

    // Validate dữ liệu đầu vào
    $request->validate([
        'title_id' => ['required', 'exists:titles,id'], // Kiểm tra danh hiệu hợp lệ
        'reward_id' => ['required', 'exists:rewards,id'], // Kiểm tra phần thưởng hợp lệ
        'achievement' => ['required', 'string', 'max:1000'], // Kiểm tra thành tích không vượt quá 1000 ký tự
    ]);

    // Tìm period_id dựa trên năm
    $period = Periods::where('year', $year)->firstOrFail();

    // Lưu đề xuất danh hiệu
    TitleNomination::create([
        'user_id' => Auth::id(),
        'unit_id' => Auth::user()->unit_id,
        'title_id' => $request->title_id,
        'period_id' => $period->id, // Lưu period_id thay vì year
        'status_id' => MetaType::where('category', 'nomination_status')
            ->where('name', 'Đề xuất')
            ->first()->id,
        'reward_id' => $request->reward_id,
        'achievement' => html_entity_decode(strip_tags($request->achievement)),
    ]);

    return redirect()->route('title-nominations.index')
        ->with('success', 'Đề xuất danh hiệu thi đua đã thành công.');
  }

  public function getList()
  {
    $nominations = TitleNomination::with(['user', 'title', 'status', 'reward'])
      ->whereHas('user')
      ->orderBy('created_at', 'desc')
      ->paginate(10);

    // Trả về view hiển thị danh sách
    return view('pages.all-title-nominations', compact('nominations'));
  }


  public function update(Request $request, $id)
  {
    $request->validate([
        'review' => ['required', 'string', 'max:1000'], 
    ]);

    $nomination = TitleNomination::findOrFail($id); 
    $nomination->update([
        'review' => html_entity_decode(strip_tags($request->input('review'))), 
    ]);

    return redirect()->route('title-nominations.list')
        ->with('success', 'Bình xét đã được cập nhật.');
  }
}
