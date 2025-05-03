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
    public function index()
    {
        $nominations = TitleNomination::with(['title', 'status'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $titles = Title::with(['level'])->get();
        $rewards = Reward::all();
        $periods = Periods::all();
        return view('pages.title-nominations', compact('nominations', 'titles', 'rewards', 'periods'));
    }

    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'period_id' => ['required', 'exists:periods,id'], // Kiểm tra period_id hợp lệ
            'title_id' => ['required', 'exists:titles,id'], // Kiểm tra danh hiệu hợp lệ
            'reward_id' => ['required', 'exists:rewards,id'], // Kiểm tra phần thưởng hợp lệ
            'achievement' => ['required', 'string', 'max:1000'], // Kiểm tra thành tích không vượt quá 1000 ký tự
        ]);

        // Lưu đề xuất danh hiệu
        TitleNomination::create([
            'user_id' => Auth::id(),
            'unit_id' => Auth::user()->unit_id,
            'title_id' => $request->title_id,
            'period_id' => $request->period_id,
            'status_id' => MetaType::where('category', 'nomination_status')
                                   ->where('name', 'Đề xuất')
                                   ->first()->id,
            'reward_id' => $request->reward_id,
            'achievement' => html_entity_decode(strip_tags($request->achievement)),
        ]);

        return redirect()->route('title-nominations.index')
            ->with('success', 'Đề xuất danh hiệu thi đua đã được gửi thành công.');
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
}