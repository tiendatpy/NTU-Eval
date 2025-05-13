<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Evaluation;
use App\Models\User;

class ExportController extends Controller
{
    public function exportEvaluation($id)
    {
        // Lấy dữ liệu đánh giá
        $evaluation = Evaluation::with(['evaluator', 'quality', 'details.criteria', 'title', 'reward'])->findOrFail($id);
        $user = $evaluation->evaluator;
        
        // Đường dẫn đến file template
        $templatePath = storage_path('app/templates/evaluation_template.docx');
        
        // Khởi tạo template processor
        $templateProcessor = new TemplateProcessor($templatePath);
        
        // Điền thông tin cơ bản
        $templateProcessor->setValue('ho_ten', $user->full_name);
        $templateProcessor->setValue('ma_cbvc', $user->id ?? '');
        $templateProcessor->setValue('trinh_do', $user->education ?? '');
        $templateProcessor->setValue('chuc_danh', $user->role ?? '');
        $templateProcessor->setValue('bo_mon', $user->department ?? '');
        $templateProcessor->setValue('don_vi', $user->unit->name ?? '');
        $templateProcessor->setValue('nam_hoc', $evaluation->period->year . ' - ' . ($evaluation->period->year + 1));
        
        // Xử lý các tiêu chí đánh giá
        $details = $evaluation->details;
        
        // Giả sử trong template có các placeholder như tieu_chi_1, minh_chung_1, muc_dat_1, v.v.
        foreach ($details as $index => $detail) {
            $i = $index + 1;
            $templateProcessor->setValue('noi_dung_' . $i, $detail->criteria->name ?? '');
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
        $templateProcessor->setValue('binh_xet', strip_tags($evaluation->review) ?? '');
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
}