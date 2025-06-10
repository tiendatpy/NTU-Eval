<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Title;
use App\Models\MetaType;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $typeFilter = $request->input('type');
        
        $titlesQuery = Title::with('type');
        
        if ($search) {
            $titlesQuery->where('name', 'like', "%{$search}%");
        }
        
        if ($typeFilter) {
            $titlesQuery->where('type_id', $typeFilter);
        }
        
        $titles = $titlesQuery->orderBy('name')->paginate(10)->withQueryString();
        $types = MetaType::where('category', 'title_type')->get();
        
        return view('admin.title.index', compact('titles', 'search', 'types', 'typeFilter'));
    }

    public function create()
    {
        $types = MetaType::where('category', 'title_type')->get();
        return view('admin.title.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:titles',
            'type_id' => 'required|exists:meta_types,id',
            'description' => 'nullable|string',
        ]);

        Title::create($request->all());

        return redirect()->route('admin.titles.index')
            ->with('success', 'Danh hiệu thi đua đã được tạo thành công.');
    }

    public function show(Title $title)
    {
        $title->load('type');
        return view('admin.title.show', compact('title'));
    }

    public function edit(Title $title)
    {
        $types = MetaType::where('category', 'title_type')->get();
        return view('admin.title.edit', compact('title', 'types'));
    }

    public function update(Request $request, Title $title)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:titles,name,' . $title->id,
            'type_id' => 'required|exists:meta_types,id',
            'description' => 'nullable|string',
        ]);

        $title->update($request->all());

        return redirect()->route('admin.titles.index')
            ->with('success', 'Danh hiệu thi đua đã được cập nhật thành công.');
    }

    public function destroy(Title $title)
    {
        // Kiểm tra xem có đánh giá nào đang sử dụng danh hiệu này không
        $hasEvaluations = 
            $title->evaluations()->exists() || 
            $title->approvedEvaluations()->exists() || 
            $title->unitEvaluations()->exists() || 
            $title->approvedUnitEvaluations()->exists();
        
        if ($hasEvaluations) {
            return redirect()->route('admin.titles.index')
                ->with('error', 'Không thể xóa danh hiệu này vì đang được sử dụng trong các đánh giá.');
        }
        
        $title->delete();

        return redirect()->route('admin.titles.index')
            ->with('success', 'Danh hiệu thi đua đã được xóa thành công.');
    }
}