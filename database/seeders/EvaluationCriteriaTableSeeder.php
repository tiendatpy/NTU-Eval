<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationCriteriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của các danh mục tiêu chí
        $teachingCatId = DB::table('meta_types')->where('category', 'criteria_category')->where('name', 'Teaching')->first()->id;
        $researchCatId = DB::table('meta_types')->where('category', 'criteria_category')->where('name', 'Research')->first()->id;
        $serviceCatId = DB::table('meta_types')->where('category', 'criteria_category')->where('name', 'Service')->first()->id;
        $devCatId = DB::table('meta_types')->where('category', 'criteria_category')->where('name', 'Professional Development')->first()->id;
        
        $criteria = [
            [
                'category_id' => $teachingCatId,
                'name' => 'Teaching Quality',
                'description' => 'Evaluation of teaching methods and effectiveness',
                'weight' => 30.00
            ],
            [
                'category_id' => $teachingCatId,
                'name' => 'Student Feedback',
                'description' => 'Student satisfaction and feedback scores',
                'weight' => 20.00
            ],
            [
                'category_id' => $researchCatId,
                'name' => 'Publications',
                'description' => 'Number and quality of research publications',
                'weight' => 25.00
            ],
            [
                'category_id' => $researchCatId,
                'name' => 'Research Grants',
                'description' => 'Ability to secure research funding',
                'weight' => 15.00
            ],
            [
                'category_id' => $serviceCatId,
                'name' => 'Community Service',
                'description' => 'Participation in community service activities',
                'weight' => 10.00
            ],
            [
                'category_id' => $devCatId,
                'name' => 'Professional Growth',
                'description' => 'Continuous learning and professional development',
                'weight' => 10.00
            ],
        ];
        
        foreach ($criteria as $criterion) {
            DB::table('evaluation_criteria')->insert([
                'category_id' => $criterion['category_id'],
                'name' => $criterion['name'],
                'description' => $criterion['description'],
                'weight' => $criterion['weight'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 