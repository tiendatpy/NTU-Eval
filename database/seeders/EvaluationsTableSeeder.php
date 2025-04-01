<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của người dùng
        $staff1 = DB::table('users')->where('username', 'staff1')->first();
        $staff2 = DB::table('users')->where('username', 'staff2')->first();
        $evaluator = DB::table('users')->where('username', 'evaluator1')->first();
        $seHead = DB::table('users')->where('username', 'sehead')->first();
        
        // Lấy ID của đơn vị
        $seDept = DB::table('units')->where('name', 'Department of Software Engineering')->first();
        $cnDept = DB::table('units')->where('name', 'Department of Computer Networks')->first();
        
        // Lấy ID của trạng thái và phân loại
        $draftStatusId = DB::table('meta_types')->where('category', 'evaluation_status')->where('name', 'Draft')->first()->id;
        $submittedStatusId = DB::table('meta_types')->where('category', 'evaluation_status')->where('name', 'Submitted')->first()->id;
        $approvedStatusId = DB::table('meta_types')->where('category', 'evaluation_status')->where('name', 'Approved')->first()->id;
        
        $excellentClassId = DB::table('meta_types')->where('category', 'evaluation_classification')->where('name', 'Excellent')->first()->id;
        $goodClassId = DB::table('meta_types')->where('category', 'evaluation_classification')->where('name', 'Good')->first()->id;
        $averageClassId = DB::table('meta_types')->where('category', 'evaluation_classification')->where('name', 'Average')->first()->id;
        
        $evaluations = [
            [
                'user_id' => $staff1->id,
                'unit_id' => $seDept->id,
                'evaluator_id' => $seHead->id,
                'period' => 2023,
                'score' => 92.50,
                'classification_id' => $excellentClassId,
                'status_id' => $approvedStatusId
            ],
            [
                'user_id' => $staff2->id,
                'unit_id' => $cnDept->id,
                'evaluator_id' => $evaluator->id,
                'period' => 2023,
                'score' => 85.75,
                'classification_id' => $goodClassId,
                'status_id' => $submittedStatusId
            ],
            [
                'user_id' => $staff1->id,
                'unit_id' => $seDept->id,
                'evaluator_id' => $evaluator->id,
                'period' => 2022,
                'score' => 78.30,
                'classification_id' => $averageClassId,
                'status_id' => $approvedStatusId
            ],
            [
                'user_id' => $staff2->id,
                'unit_id' => $cnDept->id,
                'evaluator_id' => $seHead->id,
                'period' => 2024,
                'score' => 65.00,
                'classification_id' => $averageClassId,
                'status_id' => $draftStatusId
            ],
        ];
        
        foreach ($evaluations as $evaluation) {
            DB::table('evaluations')->insert([
                'user_id' => $evaluation['user_id'],
                'unit_id' => $evaluation['unit_id'],
                'evaluator_id' => $evaluation['evaluator_id'],
                'period' => $evaluation['period'],
                'score' => $evaluation['score'],
                'classification_id' => $evaluation['classification_id'],
                'status_id' => $evaluation['status_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 