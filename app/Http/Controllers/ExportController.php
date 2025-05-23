<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Periods;
use App\Models\UnitEvaluation;

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
        if ($isUnitLeader) {
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
            $templateProcessor->setValue('ke_khai_' . $i, html_entity_decode(strip_tags($detail->evidence)) ?? '');

            // Chuyển số điểm thành text
            $rating = '';
            switch ($detail->rating) {
                case 4:
                    $rating = 'Xuất sắc';
                    break;
                case 3:
                    $rating = 'Tốt';
                    break;
                case 2:
                    $rating = 'Trung bình';
                    break;
                case 1:
                    $rating = 'Yếu';
                    break;
            }
            $templateProcessor->setValue('muc_dat_' . $i, $rating);
        }

        // Điền thông tin kết quả và xếp loại
        $templateProcessor->setValue('diem_danh_gia', (string) $evaluation->rating);
        $templateProcessor->setValue('xep_loai', $evaluation->quality->name ?? '');
        $templateProcessor->setValue('tu_nhan_xet', html_entity_decode(strip_tags($evaluation->comment)) ?? '');

        // Thông tin đề xuất danh hiệu
        $templateProcessor->setValue('danh_hieu', $evaluation->title->name ?? '');
        $templateProcessor->setValue('khen_thuong', $evaluation->reward->name ?? '');
        $templateProcessor->setValue('thanh_tich', html_entity_decode(strip_tags($evaluation->achievement)) ?? '');

        // Thông tin phê duyệt (nếu có)
        $templateProcessor->setValue('nx_uu_khuyet_diem', html_entity_decode(strip_tags($evaluation->feedback)) ?? '');
        $templateProcessor->setValue(
            'xep_loai_duyet',
            $evaluation->approved_quality_id ? $evaluation->approvedQuality->name : ''
        );
        $templateProcessor->setValue(
            'danh_hieu_duyet',
            $evaluation->approved_title_id ? $evaluation->approvedTitle->name : ''
        );

        // Tạo tên file kết quả
        $fileName = 'Phieu_danh_gia_' . $user->full_name . '_' . $evaluation->period->year . '.docx';
        $fileName = str_replace(' ', '_', $fileName);

        // Lưu file tạm thời
        $tempFilePath = storage_path('app/temp/' . $fileName);
        $templateProcessor->saveAs($tempFilePath);

        // Tải file về
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
    }

    public function exportUnitReport(Request $request)
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

        // Lấy danh sách xếp loại của cá nhân trong đơn vị
        $evaluations = Evaluation::with(['evaluator', 'quality', 'title'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id)
            ->orderBy('evaluator_id')
            ->get();

        // Lấy thông tin đánh giá đơn vị
        $unitEvaluation = UnitEvaluation::with(['quality', 'title', 'reward'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id)
            ->first();

        if ($evaluations->isEmpty() && !$unitEvaluation) {
            return back()->with('error', 'Không có dữ liệu xếp loại để xuất file.');
        }

        // Tạo file Word mới
        $templatePath = storage_path('app/templates/unit_report_template.docx');
        $templateProcessor = new TemplateProcessor($templatePath);

        // Điền thông tin cơ bản
        $templateProcessor->setValue('don_vi', $user->unit->name ?? '');
        $templateProcessor->setValue('nam_hoc', $period->year . ' - ' . ($period->year + 1));

        // PHẦN A: Đánh giá cá nhân
        // Chuẩn bị dữ liệu cho bảng đánh giá cá nhân
        $replacements = [];
        foreach ($evaluations as $index => $evaluation) {
            $replacements[] = [
                'stt' => $index + 1,
                'ho_ten' => $evaluation->evaluator->full_name ?? '',
                'muc_xep_loai' => $evaluation->quality->name ?? '',
                'danh_hieu' => $evaluation->title->name ?? '',
                'dien_giai' => html_entity_decode(strip_tags($evaluation->achievement)) ?? '',
            ];
        }

        // Áp dụng dữ liệu vào bảng
        if (!empty($replacements)) {
            $templateProcessor->cloneRowAndSetValues('stt', $replacements);
        } else {
            // Nếu không có đánh giá cá nhân, đặt giá trị trống
            $templateProcessor->setValue('stt', '');
            $templateProcessor->setValue('ho_ten', '');
            $templateProcessor->setValue('muc_xep_loai', '');
            $templateProcessor->setValue('dien_giai', '');
        }
        // PHẦN B: Đánh giá đơn vị (tập thể)
        if ($unitEvaluation) {
            // Xác định danh hiệu thi đua được phê duyệt hoặc đề xuất
            $qualityName = $unitEvaluation->quality->name;
            $unitEvidences = $unitEvaluation->evidence;
            $unitTitle = $unitEvaluation->title->name ?? '';

            $templateProcessor->setValue('xep_loai_don_vi', $qualityName);
            $templateProcessor->setValue('danh_hieu_don_vi', $unitTitle);
            $templateProcessor->setValue('minh_chung_don_vi', html_entity_decode(strip_tags($unitEvidences)));
        } else {
            // Nếu không có đánh giá đơn vị, đặt giá trị trống
            $templateProcessor->setValue('xep_loai_don_vi', '');
            $templateProcessor->setValue('minh_chung_don_vi', '');
            $templateProcessor->setValue('danh_hieu_don_vi', '');

        }

        // Tạo tên file kết quả
        $fileName = 'Bao_cao_ket_qua_danh_gia_' . $user->unit->name . '_' . $period->year . '.docx';
        $fileName = str_replace(' ', '_', $fileName);

        // Lưu file tạm thời
        $tempFilePath = storage_path('app/temp/' . $fileName);
        $templateProcessor->saveAs($tempFilePath);

        // Tải file về
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
    }




    /**
     * Xuất danh sách đề nghị danh hiệu thi đua và khen thưởng
     */
    public function exportLastReport(Request $request)
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
        $evaluations = Evaluation::with(['evaluator', 'title', 'approvedTitle', 'approvedQuality', 'reward', 'quality'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id)
            ->orderBy('evaluator_id')
            ->get();

        // Lấy thông tin đánh giá đơn vị
        $unitEvaluation = UnitEvaluation::with(['quality', 'title', 'reward'])
            ->where('period_id', $period->id)
            ->where('unit_id', $user->unit_id)
            ->first();
        
        if ($evaluations->isEmpty()) {
            return back()->with('error', 'Không có dữ liệu danh hiệu để xuất file.');
        }

        // THỐNG KÊ DANH HIỆU
        // Đếm số lượng các danh hiệu thi đua
        $titleCounts = [];
        foreach ($evaluations as $evaluation) {
            // Ưu tiên danh hiệu đã phê duyệt, nếu không có thì lấy danh hiệu đề xuất
            $titleName = $evaluation->approvedTitle ? $evaluation->approvedTitle->name : ($evaluation->title ? $evaluation->title->name : null);
            if ($titleName) {
                if (!isset($titleCounts[$titleName])) {
                    $titleCounts[$titleName] = 0;
                }
                $titleCounts[$titleName]++;
            }
        }
        
        // Đếm số lượng các hình thức khen thưởng (chỉ lấy những loại khác "Không")
        $rewardCounts = [];
        foreach ($evaluations as $evaluation) {
            if ($evaluation->reward && $evaluation->reward->name !== 'Không') {
                $rewardName = $evaluation->reward->name;
                if (!isset($rewardCounts[$rewardName])) {
                    $rewardCounts[$rewardName] = 0;
                }
                $rewardCounts[$rewardName]++;
            }
        }
        
        // Đếm số lượng tập thể
        $unitTitleName = null;
        $unitRewardName = null;
        $unitAchievement = null;

        if ($unitEvaluation) {
            if ($unitEvaluation->title) {
                $unitTitleName = $unitEvaluation->title->name;
            }
            
            // Chỉ lấy hình thức khen thưởng của đơn vị nếu khác "Không"
            if ($unitEvaluation->reward && $unitEvaluation->reward->name !== 'Không') {
                $unitRewardName = $unitEvaluation->reward->name;
                $unitAchievement = html_entity_decode(strip_tags($unitEvaluation->achievement)) ?? '';
            }
        }
        
        // Chuẩn bị thông tin thống kê
        $statisticsText = '';
        
        // Thêm thông tin thống kê danh hiệu cá nhân
        foreach ($titleCounts as $title => $count) {
            $statisticsText .= "- Danh hiệu \"{$title}\": " . $count . " cá nhân;\n";
        }
        
        // Thêm thông tin thống kê danh hiệu tập thể
        if ($unitTitleName) {
            $statisticsText .= "- Danh hiệu \"{$unitTitleName}\": 01 tập thể;\n";
        }
        
        // Thêm thông tin thống kê hình thức khen thưởng (đã loại trừ "Không")
        foreach ($rewardCounts as $reward => $count) {
            // Định dạng số có 2 chữ số (01, 02, etc.)
            $formattedCount = sprintf("%02d", $count);
            $statisticsText .= "- {$reward}: {$formattedCount} cá nhân;\n";
        }
        
        // Thêm thông tin khen thưởng của đơn vị nếu có
        if ($unitRewardName) {
            $statisticsText .= "- {$unitRewardName}: 01 tập thể;\n";
        }
        
        // Tạo file Word mới
        $templatePath = storage_path('app/templates/last_report_template.docx');
        $templateProcessor = new TemplateProcessor($templatePath);

        // Điền thông tin cơ bản
        $templateProcessor->setValue('ho_ten_tdv', $user->full_name);
        $templateProcessor->setValue('don_vi', $user->unit->name ?? '');
        $templateProcessor->setValue('ngay', date('d'));
        $templateProcessor->setValue('thang', date('m'));
        $templateProcessor->setValue('nam', date('Y'));
        $templateProcessor->setValue('ngay_thang_nam', date('d') . '/' . date('m') . '/' . date('Y'));
        $templateProcessor->setValue('nam_hoc', $period->year . ' - ' . ($period->year + 1));
        
        // Điền thông tin đánh giá đơn vị
        if ($unitEvaluation) {
            $templateProcessor->setValue('chat_luong_don_vi', $unitEvaluation->quality->name ?? '');
            $templateProcessor->setValue('danh_hieu_don_vi', $unitEvaluation->title->name ?? '');
            $templateProcessor->setValue('minh_chung_don_vi', html_entity_decode(strip_tags($unitEvaluation->evidence)) ?? '');
            $templateProcessor->setValue('hinh_thuc_khen_thuong_dv', $unitEvaluation->reward && $unitEvaluation->reward->name !== 'Không' ? $unitEvaluation->reward->name : '');
            $templateProcessor->setValue('thanh_tich_don_vi', $unitAchievement);
        } else {
            $templateProcessor->setValue('chat_luong_don_vi', '');
            $templateProcessor->setValue('danh_hieu_don_vi', '');
            $templateProcessor->setValue('minh_chung_don_vi', '');
            $templateProcessor->setValue('hinh_thuc_khen_thuong_dv', '');
            $templateProcessor->setValue('thanh_tich_don_vi', '');
        }
        
        // Điền thông tin thống kê
        $templateProcessor->setValue('thong_ke_danh_hieu', $statisticsText);

        // Phần 1: Danh sách đề nghị danh hiệu thi đua
        // Chuẩn bị dữ liệu cho bảng danh hiệu
        $titleReplacements = [];
        foreach ($evaluations as $index => $evaluation) {
            $titleReplacements[] = [
                'stt' => $index + 1,
                'ho_ten' => $evaluation->evaluator->full_name ?? '',
                'xlcl' => $evaluation->approvedQuality->name ?? '',
                'danh_hieu' => $evaluation->approvedTitle->name ?? '',
                'trich_ngang' => html_entity_decode(strip_tags($evaluation->achievement)) ?? '',
                'ghi_chu' => ''
            ];
        }

        // Áp dụng dữ liệu vào bảng danh hiệu
        $templateProcessor->cloneRowAndSetValues('stt', $titleReplacements);

        // Phần 2: Đề nghị khen thưởng
        // Chuẩn bị dữ liệu cho bảng khen thưởng
        $rewardReplacements = [];
        $rewardCounter = 1;
        foreach ($evaluations as $evaluation) {
            // Chỉ lấy những evaluation có hình thức khen thưởng
            if (!empty($evaluation->reward_id) && $evaluation->reward->name != 'Không') {
                $rewardReplacements[] = [
                    'reward_stt' => $rewardCounter,
                    'reward_ten' => $evaluation->evaluator->full_name ?? '',
                    'hinh_thuc_khen_thuong' => $evaluation->reward->name ?? '',
                    'tom_tat' => html_entity_decode(strip_tags($evaluation->achievement)) ?? '',
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
        $fileName = 'To_trinh_ket_qua_danh_gia_' . $user->unit->name . '_' . $period->year . '.docx';
        $fileName = str_replace(' ', '_', $fileName);

        // Lưu file tạm thời
        $tempFilePath = storage_path('app/temp/' . $fileName);
        $templateProcessor->saveAs($tempFilePath);

        // Tải file về
        return response()->download($tempFilePath, $fileName)->deleteFileAfterSend(true);
    }


}
