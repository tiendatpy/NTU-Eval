<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationCriteria;
use App\Models\MetaType;
use Illuminate\Http\Request;

class EvaluationCriteriaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category');
        
        $criteriaQuery = EvaluationCriteria::with('category');
        
        if ($search) {
            $criteriaQuery->where('name', 'like', "%{$search}%");
        }
        
        if ($categoryFilter) {
            $criteriaQuery->where('category_id', $categoryFilter);
        }
        
        $criteria = $criteriaQuery->orderBy('category_id')->orderBy('name')->paginate(10)->withQueryString();
        $categories = MetaType::where('category', 'criteria_category')->get();
        
        return view('admin.criteria.index', compact('criteria', 'search', 'categories', 'categoryFilter'));
    }

    public function create()
    {
        $categories = MetaType::where('category', 'criteria_category')->get();
        return view('admin.criteria.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:meta_types,id',
        ]);

        EvaluationCriteria::create($request->all());

        return redirect()->route('admin.criteria.index')
            ->with('success', 'Tiêu chí đánh giá đã được tạo thành công.');
    }

    public function show(EvaluationCriteria $criterion)
    {
        $criterion->load('category', 'details');
        return view('admin.criteria.show', compact('criterion'));
    }

    public function edit(EvaluationCriteria $criterion)
    {
        $categories = MetaType::where('category', 'criteria_category')->get();
        return view('admin.criteria.edit', compact('criterion', 'categories'));
    }

    public function update(Request $request, EvaluationCriteria $criterion)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:meta_types,id',
        ]);

        $criterion->update($request->all());

        return redirect()->route('admin.criteria.index')
            ->with('success', 'Tiêu chí đánh giá đã được cập nhật thành công.');
    }

    public function destroy(EvaluationCriteria $criterion)
    {
        // Kiểm tra xem có đánh giá nào đang sử dụng tiêu chí này không
        $hasDetails = $criterion->details()->exists();
        
        if ($hasDetails) {
            return redirect()->route('admin.criteria.index')
                ->with('error', 'Không thể xóa tiêu chí này vì đang được sử dụng trong các đánh giá.');
        }
        
        $criterion->delete();

        return redirect()->route('admin.criteria.index')
            ->with('success', 'Tiêu chí đánh giá đã được xóa thành công.');
    }
}