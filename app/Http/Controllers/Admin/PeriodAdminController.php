<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periods;
use App\Models\MetaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodAdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $yearFilter = $request->input('year');
        
        $periodsQuery = Periods::with('status');
        
        if ($search) {
            $periodsQuery->where('name', 'like', "%{$search}%");
        }
        
        if ($statusFilter) {
            $periodsQuery->where('status_id', $statusFilter);
        }
        
        if ($yearFilter) {
            $periodsQuery->where('year', $yearFilter);
        }
        
        $periods = $periodsQuery->orderBy('year', 'desc')->orderBy('start_date', 'desc')->paginate(10)->withQueryString();
        $statuses = MetaType::where('category', 'period_status')->get();
        
        // Lấy danh sách các năm duy nhất để lọc
        $years = Periods::selectRaw('DISTINCT year')->orderBy('year', 'desc')->pluck('year');
        
        return view('admin.periods.index', compact('periods', 'search', 'statuses', 'statusFilter', 'years', 'yearFilter'));
    }

    public function create()
    {
        $statuses = MetaType::where('category', 'period_status')->get();
        return view('admin.periods.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status_id' => 'required|exists:meta_types,id',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();

        Periods::create($data);

        return redirect()->route('admin.periods.index')
            ->with('success', 'Đợt đánh giá đã được tạo thành công.');
    }

    public function show(Periods $period)
    {
        $period->load('status', 'createdBy');
        
        return view('admin.periods.show', compact(
            'period'
        ));
    }

    public function edit(Periods $period)
    {
        $statuses = MetaType::where('category', 'period_status')->get();
        return view('admin.periods.edit', compact('period', 'statuses'));
    }

    public function update(Request $request, Periods $period)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status_id' => 'required|exists:meta_types,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        // Kiểm tra nếu đợt này được đánh dấu là active thì các đợt khác sẽ không active
        if ($data['is_active'] && !$period->is_active) {
            Periods::where('is_active', true)->update(['is_active' => false]);
        }

        $period->update($data);

        return redirect()->route('admin.periods.index')
            ->with('success', 'Đợt đánh giá đã được cập nhật thành công.');
    }

    public function destroy(Periods $period)
    {
        // Kiểm tra xem có đánh giá nào thuộc đợt này không
        $hasEvaluations = 
            $period->evaluations()->exists() || 
            $period->unitEvaluations()->exists();
        
        if ($hasEvaluations) {
            return redirect()->route('admin.periods.index')
                ->with('error', 'Không thể xóa đợt đánh giá này vì đã có dữ liệu đánh giá.');
        }
        
        $period->delete();

        return redirect()->route('admin.periods.index')
            ->with('success', 'Đợt đánh giá đã được xóa thành công.');
    }
    
}