<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của các đánh giá
        $evaluations = DB::table('evaluations')->get();
        
        // Lấy ID của các tiêu chí
        $teachingQuality = DB::table('evaluation_criteria')->where('name', 'Teaching Quality')->first();
        $studentFeedback = DB::table('evaluation_criteria')->where('name', 'Student Feedback')->first();
        $publications = DB::table('evaluation_criteria')->where('name', 'Publications')->first();
        $researchGrants = DB::table('evaluation_criteria')->where('name', 'Research Grants')->first();
        $communityService = DB::table('evaluation_criteria')->where('name', 'Community Service')->first();
        $professionalGrowth = DB::table('evaluation_criteria')->where('name', 'Professional Growth')->first();
        
        $criteria = [
            $teachingQuality->id,
            $studentFeedback->id,
            $publications->id,
            $researchGrants->id,
            $communityService->id,
            $professionalGrowth->id
        ];
        
        foreach ($evaluations as $evaluation) {
            foreach ($criteria as $criteriaId) {
                // Tạo điểm ngẫu nhiên từ 60 đến 100
                $score = rand(60, 100);
                
                DB::table('evaluation_details')->insert([
                    'evaluation_id' => $evaluation->id,
                    'criteria_id' => $criteriaId,
                    'score' => $score,
                    'comments' => 'Đánh giá chi tiết cho tiêu chí này.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
} 