<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\TitleNomination;
use App\Models\MetaType;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TitleNominationController extends Controller
{
    public function index()
    {
        $nominations = TitleNomination::with(['title', 'status'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $titles = Title::with(['level', 'reward'])->get();
        $rewards = Reward::all();
        return view('pages.title-nominations', compact('nominations', 'titles', 'rewards'));
    }

    public function store(Request $request)
    {
    
        $request->validate([
            'period' => ['required', 'integer', 'min:' . (now()->year - 3), 'max:' . now()->year],
            'titles' => ['required', 'array'],
            'titles.*' => ['exists:titles,id'],
            'rewards' => ['required', 'array'],
            'rewards.*' => ['exists:rewards,id'],
            'achievements' => ['required', 'array'],
            'achievements.*' => ['string', 'max:1000'],
        ]);
        
        foreach ($request->titles as $index => $titleId) {
            TitleNomination::create([
                'user_id' => Auth::id(),
                'unit_id' => Auth::user()->unit_id,
                'title_id' => $titleId,
                'period' => $request->period,
                'status_id' => MetaType::where('category', 'nomination_status')
                                       ->where('name', 'Đề xuất')
                                       ->first()->id,
                'reward_id' => $request->rewards[$index],
                'achievement' => $request->achievements[$index],
            ]);
        }

        return redirect()->route('title-nominations.index')
            ->with('success', 'Đề xuất danh hiệu thi đua đã được gửi thành công.');
    }
}