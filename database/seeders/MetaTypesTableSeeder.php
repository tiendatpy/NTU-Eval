<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetaTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $metaTypes = [
            // Loại đơn vị
            ['category' => 'unit_type', 'name' => 'Department'],
            ['category' => 'unit_type', 'name' => 'Faculty'],
            ['category' => 'unit_type', 'name' => 'Division'],
            
            // Trạng thái đánh giá
            ['category' => 'evaluation_status', 'name' => 'Draft'],
            ['category' => 'evaluation_status', 'name' => 'Submitted'],
            ['category' => 'evaluation_status', 'name' => 'Approved'],
            ['category' => 'evaluation_status', 'name' => 'Rejected'],
            
            // Phân loại đánh giá
            ['category' => 'evaluation_classification', 'name' => 'Excellent'],
            ['category' => 'evaluation_classification', 'name' => 'Good'],
            ['category' => 'evaluation_classification', 'name' => 'Average'],
            ['category' => 'evaluation_classification', 'name' => 'Below Average'],
            
            // Loại tài liệu
            ['category' => 'document_type', 'name' => 'Evaluation Form'],
            ['category' => 'document_type', 'name' => 'Certificate'],
            ['category' => 'document_type', 'name' => 'Report'],
            
            // Cấp độ giải thưởng
            ['category' => 'award_level', 'name' => 'University Level'],
            ['category' => 'award_level', 'name' => 'Department Level'],
            ['category' => 'award_level', 'name' => 'National Level'],
            
            // Trạng thái đề cử
            ['category' => 'nomination_status', 'name' => 'Pending'],
            ['category' => 'nomination_status', 'name' => 'Approved'],
            ['category' => 'nomination_status', 'name' => 'Rejected'],
            
            // Danh mục tiêu chí đánh giá
            ['category' => 'criteria_category', 'name' => 'Teaching'],
            ['category' => 'criteria_category', 'name' => 'Research'],
            ['category' => 'criteria_category', 'name' => 'Service'],
            ['category' => 'criteria_category', 'name' => 'Professional Development'],
        ];

        foreach ($metaTypes as $type) {
            DB::table('meta_types')->insert([
                'category' => $type['category'],
                'name' => $type['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 