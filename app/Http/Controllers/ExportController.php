<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Periods;

class ExportController extends Controller
{
    public function exportEvaluation($id)
    {
        $user = auth()->user();
        $isUnitLeader = $user->role->name === 'Trưởng đơn vị';
        // Lấy dữ liệu đánh giá
        $evaluation = Evaluation::with(['evaluator', 'quality', 'details.criteria', 'title', 'reward'])->findOrFail($id);
        $user = $evaluation->evaluator;
        
        // Đường dẫn đến file template
        if($isUnitLeader) {
            $templatePath = storage_path('app/templates/leader_self_evaluation_template.docx');
        } else {
            $templatePath = storage_path('app/templates/self_evaluation_template.docx');
        }
        
        // Khởi tạo template processor
        $templateProcessor = new TemplateProcessor($templatePath);
        
        // Điền thông tin cơ bản
        $templateProcessor->setValue('ho_ten', $user->full_name);
        $templateProcessor->setValue('ma_cbvc', $user->id ?? '');
        $templateProcessor->setValue('trinh_do', $user->education->name ?? '');
        $templateProcessor->setValue('chuc_danh', $user->role->name ?? '');
        // $templateProcessor->setValue('bo_mon', $user->department ?? '');
        $templateProcessor->setValue('don_vi', $user->unit->name ?? '');
        $templateProcessor->setValue('nam_hoc', $evaluation->period->year . ' - ' . ($evaluation->period->year + 1));
        
        // Xử lý các tiêu chí đánh giá
        $details = $evaluation->details;
        
        // Giả sử trong template có các placeholder như tieu_chi_1, minh_chung_1, muc_dat_1, v.v.
        foreach ($details as $index => $detail) {
            $i = $index + 1;
            // $templateProcessor->setValue('noi_dung_' . $i, $detail->criteria->name ?? '');
            $templateProcessor->setValue('ke_khai_' . $i, strip_tags($detail->evidence) ?? '');
            
            // Chuyển số điểm thành text
            $rating = '';
            switch ($detail->score) {
                case 4: $rating = 'Xuất sắc'; break;
                case 3: $rating = 'Tốt'; break;
                case 2: $rating = 'Trung bình'; break;
                case 1: $rating = 'Yếu'; break;
            }
            $templateProcessor->setValue('muc_dat_' . $i, $rating);
        }
        
        // Điền thông tin kết quả và xếp loại
        $templateProcessor->setValue('diem_danh_gia', (string) $evaluation->rating);
        $templateProcessor->setValue('xep_loai', $evaluation->quality->name ?? '');
        $templateProcessor->setValue('tu_nhan_xet', strip_tags($evaluation->comment) ?? '');
        
        // Thông tin đề xuất danh hiệu
        $templateProcessor->setValue('danh_hieu', $evaluation->title->name ?? '');
        $templateProcessor->setValue('khen_thuong', $evaluation->reward->name ?? '');
        $templateProcessor->setValue('thanh_tich', strip_tags($evaluation->achievement) ?? '');
        
        // Thông tin phê duyệt (nếu có)
        $templateProcessor->setValue('nx_uu_khuyet_diem', strip_tags($evaluation->feedback) ?? '');
        $templateProcessor->setValue('xep_loai_duyet', 
            $evaluation->approved_quality_id ? $evaluation->approvedQuality->name : '');
        $templateProcessor->setValue('danh_hieu_duyet', 
            $evaluation->approved_title_id ? $evaluation->approvedTitle->name : '');
        
        // Tạo tên file kết quả
        $fileName = 'Phieu_danh_gia_' . $user->full_name . '_' . $evaluation->period->year . '.docx';
        $fileName = str_replace(' ', '_', $fileName);
        
        // Lưu file tạm thời
        $tempFilePath = storage_path('app/temp/' . $fileName);
        $templateProcessor->saveAs($tempFilePath);
        
        // Tải file về
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
    }

    public function exportQualityList(Request $request)
    {
        $user = auth()->user();
        if ($user->role->name !== 'Trưởng đơn vị') {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }
        
        // Lấy tham số từ request
        $year = $request->input('year', now()->year - 1);
        $period = Periods::where('year', $year)->first();
        
        if (!$period) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }
        
        // Lấy danh sách xếp loại của đơn vị
        $evaluations = Evaluation::with(['evaluator', 'quality', 'approvedQuality'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id)
            ->where('evaluator_id', '!=', $user->id) // Loại trừ trưởng đơn vị
            ->orderBy('evaluator_id')
            ->get();
        
        if ($evaluations->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu xếp loại để xuất file.');
        }
        
        // Tạo file Word mới
        $templatePath = storage_path('app/templates/quality_ratings_template.docx');
        $templateProcessor = new TemplateProcessor($templatePath);
        
        // Điền thông tin cơ bản
        $templateProcessor->setValue('don_vi', $user->unit->name ?? '');
        $templateProcessor->setValue('nam_hoc', $period->year . ' - ' . ($period->year + 1));
        
        // Chuẩn bị dữ liệu cho bảng
        $replacements = [];
        foreach ($evaluations as $index => $evaluation) {
            $approvedQuality = $evaluation->approved_quality_id ? 
                $evaluation->approvedQuality->name : $evaluation->quality->name;
            $replacements[] = [
                'stt' => $index + 1,
                'ho_ten' => $evaluation->evaluator->full_name ?? '',
                'muc_xep_loai' => $approvedQuality ?? '',
                'dien_giai' => $this->formatEvidences($evaluation)
            ];
        }
        
        // Áp dụng dữ liệu vào bảng
        $templateProcessor->cloneRowAndSetValues('stt', $replacements);
        
        // Tạo tên file kết quả
        $fileName = 'Danh_sach_xep_loai_chat_luong_' . $user->unit->name . '_' . $period->year . '.docx';
        $fileName = str_replace(' ', '_', $fileName);
        
        // Lưu file tạm thời
        $tempFilePath = storage_path('app/temp/' . $fileName);
        $templateProcessor->saveAs($tempFilePath);
        
        // Tải file về
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Định dạng bằng chứng từ evaluation details
     */
    private function formatEvidences(Evaluation $evaluation)
    {
        $evidences = [];
        
        // Thêm các thông tin cần thiết vào diễn giải
        // Ví dụ: Hoàn thành vượt định mức giờ giảng và nghiên cứu
        if ($evaluation->details) {
            foreach ($evaluation->details as $detail) {
                if ($detail->evidence && !empty(trim($detail->evidence))) {
                    $evidences[] = '- ' . strip_tags($detail->evidence);
                }
            }
        }
        
        return implode("\n", $evidences);
    }

    /**
     * Xuất danh sách đề nghị danh hiệu thi đua và khen thưởng
     */
    public function exportTitleNominations(Request $request)
    {
        $user = auth()->user();
        if ($user->role->name !== 'Trưởng đơn vị') {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }
        
        // Lấy tham số từ request
        $year = $request->input('year', now()->year - 1);
        $period = Periods::where('year', $year)->first();
        
        if (!$period) {
            return back()->with('error', 'Không tìm thấy kỳ đánh giá cho năm đã chọn.');
        }
        
        // Lấy danh sách đề nghị danh hiệu của đơn vị
        $nominations = Evaluation::with(['evaluator', 'title', 'approvedTitle', 'reward', 'quality'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id)
            ->where('evaluator_id', '!=', $user->id) // Loại trừ trưởng đơn vị
            ->orderBy('evaluator_id')
            ->get();
        
        if ($nominations->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu danh hiệu để xuất file.');
        }
        
        // Tạo file Word mới
        $templatePath = storage_path('app/templates/title_nominations_template.docx');
        $templateProcessor = new TemplateProcessor($templatePath);
        
        // Điền thông tin cơ bản
        $templateProcessor->setValue('don_vi', $user->unit->name ?? '');
        $templateProcessor->setValue('nam_hoc', $period->year . ' - ' . ($period->year + 1));
        $templateProcessor->setValue('ngay_to_trinh', date('d/m/Y'));
        
        // Phần 1: Danh sách đề nghị danh hiệu thi đua
        // Chuẩn bị dữ liệu cho bảng danh hiệu
        $titleReplacements = [];
        foreach ($nominations as $index => $nomination) {
            $approvedTitle = $nomination->approved_title_id ? $nomination->approvedTitle->name : $nomination->title->name;
            
            $titleReplacements[] = [
                'stt' => $index + 1,
                'ho_ten' => $nomination->evaluator->full_name ?? '',
                'xlcl' => $nomination->quality->name ?? '',
                'danh_hieu' => $approvedTitle ?? '',
                'trich_ngang' => $this->formatAchievements($nomination),
                'ghi_chu' => ''
            ];
        }
        
        // Áp dụng dữ liệu vào bảng danh hiệu
        $templateProcessor->cloneRowAndSetValues('stt', $titleReplacements);
        
        // Phần 2: Đề nghị khen thưởng
        // Chuẩn bị dữ liệu cho bảng khen thưởng
        $rewardReplacements = [];
        $rewardCounter = 1;
        foreach ($nominations as $nomination) {
            // Chỉ lấy những nomination có hình thức khen thưởng
            if (!empty($nomination->reward_id) && $nomination->reward->name != 'Không') {
                $rewardReplacements[] = [
                    'reward_stt' => $rewardCounter,
                    'reward_ten' => $nomination->evaluator->full_name ?? '',
                    'hinh_thuc_khen_thuong' => $this->formatRewardType($nomination->reward->name),
                    'tom_tat' => $this->formatRewardAchievements($nomination)
                ];
                $rewardCounter++;
            }
        }
        
        // Áp dụng dữ liệu vào bảng khen thưởng nếu có
        if (!empty($rewardReplacements)) {
            $templateProcessor->cloneRowAndSetValues('reward_stt', $rewardReplacements);
        } else {
            // Nếu không có đề nghị khen thưởng, có thể xóa phần này hoặc để trống
            $templateProcessor->setValue('reward_stt', '');
            $templateProcessor->setValue('reward_ten', '');
            $templateProcessor->setValue('hinh_thuc_khen_thuong', '');
            $templateProcessor->setValue('tom_tat', '');
        }
        
        // Tạo tên file kết quả
        $fileName = 'Danh_sach_danh_hieu_thi_dua_' . $user->unit->name . '_' . $period->year . '.docx';
        $fileName = str_replace(' ', '_', $fileName);
        
        // Lưu file tạm thời
        $tempFilePath = storage_path('app/temp/' . $fileName);
        $templateProcessor->saveAs($tempFilePath);
        
        // Tải file về
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Định dạng thành tích từ evaluation
     */
    private function formatAchievements(Evaluation $evaluation)
    {
        $achievements = [];
        // Thêm thành tích từ field achievement
        if (!empty(trim($evaluation->achievement))) {
            $achievements[] = '- ' . strip_tags($evaluation->achievement);
        }
        
        return implode("\n", $achievements);
    }

    /**
     * Định dạng thành tích cho phần khen thưởng
     */
    private function formatRewardAchievements(Evaluation $evaluation)
    {
        $achievements = [];
        
        // Thêm thành tích từ field achievement với format chi tiết hơn
        if (!empty(trim($evaluation->achievement))) {
            $achievementText = strip_tags($evaluation->achievement);
            
            // Có thể thêm tiền tố hoặc định dạng theo mẫu
            $achievements[] = $achievementText;
        }
        
        
        return implode("\n", $achievements);
    }

    /**
     * Định dạng loại khen thưởng
     */
    private function formatRewardType($rewardName)
    {
        $rewardTypes = [
            'Giấy khen' => 'Giấy khen Hiệu trưởng',
            'Bằng khen' => 'Bằng khen (BK)',
            'Kỷ niệm chương' => 'Kỷ niệm chương (KNC)',
            'Huân chương Lao động' => 'Huân chương Lao động (HCLĐ)',
            // Thêm các mapping khác nếu cần
        ];
        
        return $rewardTypes[$rewardName] ?? $rewardName;
    }
}