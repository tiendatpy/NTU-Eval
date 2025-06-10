<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $rewardsQuery = Reward::query();
        
        if ($search) {
            $rewardsQuery->where('name', 'like', "%{$search}%");
        }
        
        $rewards = $rewardsQuery->orderBy('name')->paginate(10)->withQueryString();
        
        return view('admin.reward.index', compact('rewards', 'search'));
    }

    public function create()
    {
        return view('admin.reward.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rewards',
            'description' => 'nullable|string',
        ]);

        Reward::create($request->all());

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Hình thức khen thưởng đã được tạo thành công.');
    }

    public function show(Reward $reward)
    {
        return view('admin.reward.show', compact('reward'));
    }

    public function edit(Reward $reward)
    {
        return view('admin.reward.edit', compact('reward'));
    }

    public function update(Request $request, Reward $reward)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rewards,name,' . $reward->id,
            'description' => 'nullable|string',
        ]);

        $reward->update($request->all());

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Hình thức khen thưởng đã được cập nhật thành công.');
    }

    public function destroy(Reward $reward)
    {
        // Kiểm tra xem có đánh giá nào đang sử dụng hình thức khen thưởng này không
        $hasEvaluations = 
            $reward->evaluations()->exists() || 
            $reward->approvedEvaluations()->exists() || 
            $reward->unitEvaluations()->exists() || 
            $reward->approvedUnitEvaluations()->exists();
        
        if ($hasEvaluations) {
            return redirect()->route('admin.rewards.index')
                ->with('error', 'Không thể xóa hình thức khen thưởng này vì đang được sử dụng trong các đánh giá.');
        }
        
        $reward->delete();

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Hình thức khen thưởng đã được xóa thành công.');
    }
}