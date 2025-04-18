<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\EvaluationDetail;
use App\Models\EvaluationCriteria;
use App\Models\MetaType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Document;

class EvaluationController extends Controller
{
    public function index()
    {
        $criteria = EvaluationCriteria::where('category_id', 30)->get();
        $evaluation = Evaluation::latest()->first();
        return view('pages.self-evaluation', compact('criteria', 'evaluation'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thực hiện hành động này.');
        }
        $request->validate([
            'period' => 'required|digits:4',
            'details' => 'required|array',
            'details.*.criteria_id' => 'required|exists:evaluation_criteria,id',
            'details.*.score' => 'required|numeric|min:1|max:4',
            'details.*.evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        Log::info('Request data: ', $request->all());

        DB::beginTransaction();


        try {
            $sum = 0;
            $count = 0;
            $scores = [];

            foreach ($request->details as $detail) {
                $scores[] = $detail['score'];
                $sum += $detail['score'];
                $count++;
            }

            $finalScore = $count > 0 ? round($sum / $count, 2) : 0;

            $classificationName = 'Không hoàn thành';

            if (
                isset($scores[0], $scores[1], $scores[2]) &&
                $scores[0] == 4 && $scores[1] == 4 && $scores[2] == 4 &&
                collect($scores)->every(fn($s) => $s >= 3)
            ) {
                $classificationName = 'Hoàn thành xuất sắc';
            } elseif (collect($scores)->every(fn($s) => $s >= 3)) {
                $classificationName = 'Hoàn thành tốt';
            } elseif (collect($scores)->every(fn($s) => $s >= 2)) {
                $classificationName = 'Hoàn thành';
            }

            $classification = MetaType::where('category', 'evaluation_classification')
                ->where('name', $classificationName)
                ->first();

            $status = MetaType::where('category', 'evaluation_status')
                ->where('name', 'Tự đánh giá') // Giá trị mặc định cho status_id
                ->first();

            Log::info('Attempting to create Evaluation...');
            $evaluation = Evaluation::create([
                'user_id' => $user->id,
                'unit_id' => $user->unit_id,
                'evaluator_id' => $user->id,
                'period' => $request->period,
                'score' => $finalScore,
                'classification_id' => $classification?->id,
                'status_id' => $status?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::info('Evaluation created: ', $evaluation->toArray());

            foreach ($request->details as $detail) {
                $evaluationDetail = EvaluationDetail::create([
                    'evaluation_id' => $evaluation->id,
                    'criteria_id' => $detail['criteria_id'],
                    'score' => $detail['score'],
                    'comments' => $detail['comments'] ?? null,
                ]);

                // Xử lý upload file minh chứng
                if (isset($detail['evidence']) && $detail['evidence']->isValid()) {
                    $evidencePath = $detail['evidence']->store('evidences', 'public');

                    // Lưu minh chứng vào bảng documents
                    Document::create([
                        'type_id' => MetaType::where('category', 'document_type')->where('name', 'Minh chứng')->first()->id,
                        'file_name' => $detail['evidence']->getClientOriginalName(),
                        'file_path' => $evidencePath,
                        'evaluation_detail_id' => $evaluationDetail->id,
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('evaluations.index')->with('success', 'Đánh giá đã được lưu.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error occurred: ' . $e->getMessage());
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
