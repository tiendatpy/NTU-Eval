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

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        $isUnitLeader = $user->role->name === 'Trưởng đơn vị'; // Kiểm tra vai trò trưởng đơn vị

        $selectedYear = $request->input('year', now()->year - 1); // Mặc định là năm hiện tại - 1

        // Tìm kỳ đánh giá theo năm
        $currentPeriod = Periods::where('year', $selectedYear)->first();

        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }

        // Tìm đánh giá của người dùng cho kỳ đánh giá đó
        $evaluation = Evaluation::where('evaluator_id', $user->id)
            ->where('period_id', $currentPeriod->id)
            ->with(['details.criteria'])
            ->first();

        // Lấy tiêu chí đánh giá phù hợp với vai trò
        $managerId = Cache::remember('criteria_category_manager_id', 86400, function () {
            return MetaType::where('name', 'Viên chức, NLĐ quản lý')->value('id');
        });
        $staffId = Cache::remember('criteria_category_staff_id', 86400, function () {
            return MetaType::where('name', 'Viên chức, NLD không quản lý')->value('id');
        });
        if ($isUnitLeader) {
            $criteria = EvaluationCriteria::where('category_id', $managerId)->get();
        } else {
            $criteria = EvaluationCriteria::where('category_id', $staffId)->get();
        }

        // Lấy dữ liệu cần thiết cho form
        $periods = Periods::orderBy('year', 'desc')->get();
        $quality = Quality::all();

        $titleTypeId = Cache::remember('title_type_id', 86400, function () {
            return MetaType::where('category', 'type_title')
                ->where('name', 'Cá nhân')
                ->value('id');
        });
        $titles = Title::where('type_id', $titleTypeId)->get();

        $rewards = Reward::all();

        $isCurrentYear = $selectedYear == now()->year - 1;

        if ($request->ajax()) {
            // Trả về HTML của bảng đánh giá qua AJAX
            $html = view('pages.partials.evaluation-table', compact(
                'evaluation',
                'isCurrentYear',
                'criteria',
                'quality',
                'isUnitLeader',
                'titles',
                'rewards'
            ))->render();

            return response()->json(['html' => $html]);
        }

        return view('pages.self-evaluation', compact(
            'evaluation',
            'criteria',
            'periods',
            'quality',
            'titles',
            'rewards',
            'selectedYear',
            'isCurrentYear',
            'isUnitLeader'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Lấy period_id từ kỳ đánh giá của năm hiện tại - 1
        $currentPeriod = Periods::where('year', now()->year - 1)->first();
        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm hiện tại.');
        }
        $periodId = $currentPeriod->id;

        // Validate dữ liệu đầu vào
        $request->validate([
            'quality_id' => 'required|exists:quality,id',
            'details' => 'required|array',
            'details.*.criteria_id' => 'required|exists:evaluation_criteria,id',
            'details.*.rating' => 'required|numeric|min:1|max:4',
            'details.*.evidence' => 'nullable|string',
            'comment' => 'nullable|string',
            'title_id' => 'required|exists:titles,id',
            'reward_id' => 'required|exists:rewards,id',
            'achievement' => 'nullable|required|string',
        ]);

        DB::beginTransaction();

        try {
            // Tính toán điểm đánh giá
            $sum = 0;
            $count = 0;
            foreach ($request->details as $detail) {
                $sum += $detail['rating'];
                $count++;
            }
            $finalRating = $count > 0 ? round($sum / $count, 2) : 0;

            $status = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Đang xét duyệt')
                ->first();

            if (!$status) {
                throw new \Exception('Không tìm thấy trạng thái đánh giá.');
            }

            // Kiểm tra xem đã tồn tại đánh giá cho kỳ đánh giá này chưa
            $existingEvaluation = Evaluation::where('evaluator_id', $user->id)
                ->where('period_id', $periodId)
                ->first();

            if ($existingEvaluation) {
                // Cập nhật đánh giá hiện có
                $existingEvaluation->update([
                    'rating' => $finalRating,
                    'quality_id' => $request->quality_id,
                    'title_id' => $request->title_id,
                    'reward_id' => $request->reward_id,
                    'achievement' => $request->achievement,
                    'comment' => $request->comment,
                    'status_id' => $status->id, // Cập nhật lại trạng thái thành "Đang xét duyệt"
                    'updated_at' => now(),
                ]);

                // Cập nhật chi tiết đánh giá
                foreach ($request->details as $detail) {
                    $existingDetail = EvaluationDetail::where('evaluation_id', $existingEvaluation->id)
                        ->where('criteria_id', $detail['criteria_id'])
                        ->first();

                    if ($existingDetail) {
                        // Cập nhật chi tiết hiện có
                        $existingDetail->update([
                            'rating' => $detail['rating'],
                            'evidence' => $detail['evidence'],
                        ]);
                    } else {
                        // Tạo chi tiết mới nếu không tồn tại
                        EvaluationDetail::create([
                            'evaluation_id' => $existingEvaluation->id,
                            'criteria_id' => $detail['criteria_id'],
                            'rating' => $detail['rating'],
                            'evidence' => $detail['evidence'],
                        ]);
                    }
                }

                $evaluation = $existingEvaluation;
                $message = 'Cập nhật đánh giá thành công.';
            } else {
                // Tạo đánh giá mới
                $evaluation = Evaluation::create([
                    'evaluator_id' => $user->id,
                    'unit_id' => $user->unit_id,
                    'period_id' => $periodId,
                    'rating' => $finalRating,
                    'quality_id' => $request->quality_id,
                    'approved_quality_id' => null,
                    'title_id' => $request->title_id,
                    'approved_title_id' => null,
                    'reward_id' => $request->reward_id,
                    'achievement' => $request->achievement,
                    'comment' => $request->comment,
                    'review' => null,
                    'feedback' => null,
                    'status_id' => $status->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Tạo chi tiết đánh giá
                foreach ($request->details as $detail) {
                    EvaluationDetail::create([
                        'evaluation_id' => $evaluation->id,
                        'criteria_id' => $detail['criteria_id'],
                        'rating' => $detail['rating'],
                        'evidence' => $detail['evidence'],
                    ]);
                }

                $message = 'Tự đánh giá thành công.';
            }

            DB::commit();

            return redirect()->route('evaluations.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function result()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Lấy kỳ đánh giá của năm hiện tại - 1
        $currentPeriod = Periods::where('year', now()->year - 1)->first();
        if (!$currentPeriod) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm hiện tại.');
        }

        // Tìm đánh giá của người dùng cho kỳ đánh giá đó
        $evaluation = Evaluation::where('evaluator_id', $user->id)
            ->where('period_id', $currentPeriod->id)
            ->with(['details.criteria', 'quality', 'title', 'reward', 'approvedQuality', 'approvedTitle'])
            ->first();

        if (!$evaluation) {
            return redirect()->route('evaluations.index')
                ->with('error', 'Không tìm thấy đánh giá của bạn cho kỳ đánh giá này.');
        }

        // Lấy tiêu chí đánh giá phù hợp với vai trò
        $isUnitLeader = $user->role->name === 'Trưởng đơn vị';
        $managerId = Cache::remember('criteria_category_manager_id', 86400, function () {
            return MetaType::where('name', 'Viên chức, NLĐ quản lý')->value('id');
        });
        $staffId = Cache::remember('criteria_category_staff_id', 86400, function () {
            return MetaType::where('name', 'Viên chức, NLD không quản lý')->value('id');
        });

        if ($isUnitLeader) {
            $criteria = EvaluationCriteria::where('category_id', $managerId)->get();
        } else {
            $criteria = EvaluationCriteria::where('category_id', $staffId)->get();
        }

        $isCurrentYear = true; // Luôn true vì chúng ta đang xem đánh giá năm hiện tại

        // Trả về view với partial evaluation-result
        return view('pages.self-evaluation-result', compact(
            'evaluation',
            'isCurrentYear',
            'criteria',
            'isUnitLeader'
        ));
    }


    // public function getListTitleNomination(Request $request)
    // {
    //     $user = auth()->user();
    //     if (!$user) {
    //         return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
    //     }

    //     $years = Periods::orderBy('year', 'desc')->pluck('year')->unique();

    //     $defaultYear = now()->year - 1;

    //     if (!$years->contains($defaultYear)) {
    //         $defaultYear = $years->first(); // Nếu không có, lấy năm gần nhất
    //     }

    //     $selectedYear = $request->input('year', $defaultYear);

    //     $period = Periods::where('year', $selectedYear)->first();

    //     if (!$period) {
    //         return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
    //     }

    //     // Lấy danh sách các title để hiển thị trong dropdown
    //     $titleTypeId = Cache::remember('title_type_id', 86400, function () {
    //         return MetaType::where('category', 'type_title')
    //             ->where('name', 'Cá nhân')
    //             ->value('id');
    //     });
    //     $titles = Title::where('type_id', $titleTypeId)->get();

    //     $query = Evaluation::with(['evaluator', 'title', 'reward', 'status'])
    //         ->where('period_id', $period->id)
    //         ->where('unit_id', $user->unit_id);

    //     // Nếu là trưởng đơn vị, lọc bỏ bản thân ra khỏi danh sách
    //     if ($user->role->name === 'Trưởng đơn vị') {
    //         $query->where('evaluator_id', '!=', $user->id); // Thay đổi ở đây
    //     }

    //     $nominations = $query->orderBy('created_at', 'asc')
    //         ->paginate(5);

    //     if ($user->role->name === 'Trưởng đơn vị') {
    //         return view('pages.unit-leader.title-approvals', compact('nominations', 'years', 'selectedYear', 'titles'));
    //     } else {
    //         return view('pages.all-title-nominations', compact('nominations', 'years', 'selectedYear'));
    //     }
    // }

    public function getListEvaluation(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        $isUnitLeader = $user->role->name === 'Trưởng đơn vị';

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

        if (!$period) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }

        // Xây dựng query cơ bản
        $query = Evaluation::where('period_id', $period->id)
            ->with([
                'evaluator',
                'quality',
                'approvedQuality',
                'title',
                'approvedTitle',
                'reward',
                'status',
                'unit'
            ])->orderBy('created_at', 'asc');
        if (!$isUnitLeader) {
            $query->where('unit_id', $user->unit_id)
                ->where('evaluator_id', '!=', $user->id); // Loại trừ bản thân trưởng đơn vị
        }
        else {
            $query->where('unit_id', $user->unit_id);
        }

        // Lọc theo đơn vị nếu có
        if ($request->has('unit_id') && $request->unit_id) {
            $query->where('unit_id', $request->unit_id);
        }

        // Lọc theo xếp loại chất lượng nếu có
        if ($request->has('quality_id') && $request->quality_id) {
            $query->where('quality_id', $request->quality_id);
        }

        // Lọc theo danh hiệu thi đua nếu có
        if ($request->has('title_id') && $request->title_id) {
            $query->where('title_id', $request->title_id);
        }

        // Lọc theo trạng thái nếu có
        if ($request->has('status_id') && $request->status_id) {
            $query->where('status_id', $request->status_id);
        }

        // Lấy danh sách đánh giá đã lọc
        $evaluations = $query->orderBy('created_at', 'desc')->paginate(5);

        // Lấy danh sách xếp loại chất lượng để hiện thị trong dropdown filter
        $qualities = Quality::all();

        // Lấy danh sách danh hiệu thi đua để hiển thị trong dropdown filter
        $titleTypeId = Cache::remember('title_type_id', 86400, function () {
            return MetaType::where('category', 'type_title')
                ->where('name', 'Cá nhân')
                ->value('id');
        });
        $titles = Title::where('type_id', $titleTypeId)->get();

        // Lấy danh sách trạng thái để hiển thị trong dropdown filter
        $statuses = MetaType::where('category', 'evaluation_status')->get();

        // Return view hoặc JSON data tùy theo request type
        // if ($request->ajax()) {
        //     return response()->json([
        //         'html' => view('pages.partials.evaluation-list-table', compact('evaluations'))->render(),
        //     ]);
        // }

        return view('pages.partials.self-evaluation-list', compact(
            'evaluations',
            'years',
            'selectedYear',
            'qualities',
            'titles',
            'statuses',
            'isUnitLeader'

        ));
    }

    public function addReview(Request $request, Evaluation $evaluation)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }
        
        // Kiểm tra quyền: chỉ cho phép người dùng không phải trưởng đơn vị trong cùng đơn vị
        if ($user->role->name === 'Trưởng đơn vị' || $user->unit_id !== $evaluation->unit_id) {
            return back()->with('error', 'Bạn không có quyền thêm nhận xét này.');
        }
        
        // Validate dữ liệu
        $request->validate([
            'review' => 'string',
        ]);
        
        
        try {
            // Cập nhật trường review
            $evaluation->update([
                'review' => $request->review,
            ]);
            
            return redirect()->route('all-evaluations.view-details', $evaluation->id)
                ->with('success', 'Đã thêm góp thành công.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
    }
    }


    public function viewDetails(Evaluation $evaluation)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }

        // Load các quan hệ cần thiết
        $evaluation->load([
            'details.criteria',
            'quality',
            'title',
            'reward',
            'approvedQuality',
            'approvedTitle',
            'status',
            'evaluator',
            'unit'
        ]);

        // Xác định vai trò của người xem
        $isUnitLeader = $user->role->name === 'Trưởng đơn vị';
        $isCurrentUserEvaluation = $user->id === $evaluation->evaluator_id;

        // Lấy danh sách xếp loại và danh hiệu cho dropdown
        $qualities = Quality::all();

        $titleTypeId = Cache::remember('title_type_id', 86400, function () {
            return MetaType::where('category', 'type_title')
                ->where('name', 'Cá nhân')
                ->value('id');
        });
        $titles = Title::where('type_id', $titleTypeId)->get();

        return view('pages.partials.self-evaluation-details', compact(
            'evaluation',
            'isUnitLeader',
            'isCurrentUserEvaluation',
            'qualities',
            'titles'
        ));
    }

    public function approveDetails(Request $request, Evaluation $evaluation)
    {
        $user = auth()->user();

        if (!$user || $user->role->name !== 'Trưởng đơn vị') {
            return redirect()->route('login')
                ->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        // Kiểm tra xem người dùng có phải là trưởng đơn vị của người đánh giá không
        if ($user->unit_id != $evaluation->unit_id) {
            return back()->with('error', 'Bạn không có quyền phê duyệt đánh giá này.');
        }

        // Validate dữ liệu đầu vào
        $request->validate([
            'approved_quality_id' => 'required|exists:quality,id',
            'approved_title_id' => 'required|exists:titles,id',
            'feedback' => 'nullable|string',
        ]);



        try {
            DB::beginTransaction();

            // Cập nhật trạng thái thành "Đã phê duyệt"
            $approvedStatus = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Đã phê duyệt')
                ->firstOrFail();

            // Cập nhật đánh giá
            $evaluation->update([
                'approved_quality_id' => $request->approved_quality_id,
                'approved_title_id' => $request->approved_title_id,
                'feedback' => $request->feedback ,
                'status_id' => $approvedStatus->id,
            ]);


            DB::commit();

            return redirect()->route('evaluations.list')
                ->with('success', 'Đã phê duyệt đánh giá thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
