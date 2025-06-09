<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quality;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $qualitiesQuery = Quality::query();
        
        if ($search) {
            $qualitiesQuery->where('name', 'like', "%{$search}%");
        }
        
        $qualities = $qualitiesQuery->orderBy('name')->paginate(10)->withQueryString();
        
        return view('admin.quality.index', compact('qualities', 'search'));
    }

    public function create()
    {
        return view('admin.quality.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:quality',
            'description' => 'nullable|string',
        ]);

        Quality::create($request->all());

        return redirect()->route('admin.qualities.index')
            ->with('success', 'Xếp loại chất lượng đã được tạo thành công.');
    }

    public function show(Quality $quality)
    {
        return view('admin.quality.show', compact('quality'));
    }

    public function edit(Quality $quality)
    {
        return view('admin.quality.edit', compact('quality'));
    }

    public function update(Request $request, Quality $quality)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:quality,name,' . $quality->id,
            'description' => 'nullable|string',
        ]);

        $quality->update($request->all());

        return redirect()->route('admin.qualities.index')
            ->with('success', 'Xếp loại chất lượng đã được cập nhật thành công.');
    }

    public function destroy(Quality $quality)
    {
        // Kiểm tra xem có đánh giá nào đang sử dụng xếp loại này không
        $hasEvaluations = 
            $quality->evaluations()->exists() || 
            $quality->approvedEvaluations()->exists() || 
            $quality->unitEvaluations()->exists() || 
            $quality->approvedUnitEvaluations()->exists();
        
        if ($hasEvaluations) {
            return redirect()->route('admin.qualities.index')
                ->with('error', 'Không thể xóa xếp loại này vì đang được sử dụng trong các đánh giá.');
        }
        
        $quality->delete();

        return redirect()->route('admin.qualities.index')
            ->with('success', 'Xếp loại chất lượng đã được xóa thành công.');
    }
}