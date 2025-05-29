<?php
namespace App\Http\Controllers;

use App\Models\Periods;
use App\Models\MetaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeriodController extends Controller
{
    /**
     * Hiển thị danh sách đợt đánh giá của đơn vị
     */
    public function index()
    {
        
        // Lấy tất cả đợt đánh giá
        $periods = Periods::with(['status', 'createdBy'])
            ->orderBy('year', 'desc')
            ->orderBy('start_date', 'desc')
            ->paginate(10);
        
        // Lấy danh sách trạng thái đợt đánh giá
        $statuses = MetaType::where('category', 'period_status')->get();
        
        // Lấy ID của trạng thái "Đang diễn ra" (mở) và "Kết thúc" (đóng)
        $openStatusId = MetaType::where('category', 'period_status')
            ->where('name', 'Mở')
            ->value('id');
            
        $closedStatusId = MetaType::where('category', 'period_status')
            ->where('name', 'Đóng')
            ->value('id');
        
        return view('pages.unit-leader.periods.period-management', compact(
            'periods', 
            'statuses', 
            'openStatusId', 
            'closedStatusId'
        ));
    }
    
    /**
     * Hiển thị form tạo đợt đánh giá mới
     */
    public function create()
    {

        $statuses = MetaType::where('category', 'period_status')->get();
        
        return view('pages.unit-leader.periods.period-create', compact('statuses'));
    }
      /**
     * Lưu đợt đánh giá mới
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status_id' => 'required|exists:meta_types,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Kiểm tra xem đã có đợt đánh giá nào trong năm này chưa
            $existingPeriod = Periods::where('year', $request->year)->first();
            if ($existingPeriod) {
                return redirect()->back()->with('error', 'Đã tồn tại đợt đánh giá trong năm học ' . $request->year . ' - ' . ($request->year + 1))->withInput();
            }
            
            Periods::create([
                'year' => $request->year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status_id' => $request->status_id,
                'is_active' => true,
                'created_by' => $user->id,
            ]);
            
            DB::commit();
            
            return redirect()->route('periods.index')->with('success', 'Đợt đánh giá đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Hiển thị form chỉnh sửa đợt đánh giá
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->role->isUnitLeader) {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập tính năng này.');
        }
        
        $period = Periods::findOrFail($id);
        $statuses = MetaType::where('category', 'period_status')->get();
        
        return view('pages.unit-leader.periods.period-edit', compact('period', 'statuses'));
    }
      /**
     * Cập nhật đợt đánh giá
     */
    public function update(Request $request, $id)
    {
        $period = Periods::findOrFail($id);
        
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status_id' => 'required|exists:meta_types,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Kiểm tra xem đã có đợt đánh giá nào khác trong năm này chưa
            if ($period->year != $request->year) {
                $existingPeriod = Periods::where('year', $request->year)
                    ->where('id', '!=', $period->id)
                    ->first();
                
                if ($existingPeriod) {
                    return redirect()->back()->with('error', 'Đã tồn tại đợt đánh giá khác trong năm học ' . $request->year . ' - ' . ($request->year + 1))->withInput();
                }
            }
            
            $period->update([
                'year' => $request->year,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status_id' => $request->status_id,
            ]);
            
            DB::commit();
            
            return redirect()->route('periods.index')->with('success', 'Đợt đánh giá đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Chuyển đổi trạng thái mở/đóng đợt đánh giá
     */
    public function toggleStatus($id)
    {
        
        $period = Periods::findOrFail($id);
        
        try {
            // Lấy ID của trạng thái "Đang diễn ra" (mở) và "Kết thúc" (đóng)
            $openStatusId = MetaType::where('category', 'period_status')
                ->where('name', 'Mở')
                ->value('id');
                
            $closedStatusId = MetaType::where('category', 'period_status')
                ->where('name', 'Đóng')
                ->value('id');
            
            // Nếu hiện tại đang mở, chuyển sang đóng và ngược lại
            $newStatusId = ($period->status_id == $openStatusId) ? $closedStatusId : $openStatusId;
            
            $period->status_id = $newStatusId;
            $period->save();
            
            $status = ($newStatusId == $openStatusId) ? 'mở' : 'đóng';
            
            return response()->json([
                'success' => true, 
                'message' => "Đã $status đợt đánh giá thành công.",
                'is_open' => ($newStatusId == $openStatusId)
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
        }
    }
}