<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AwardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của các cấp độ giải thưởng
        $uniLevelId = DB::table('meta_types')->where('category', 'award_level')->where('name', 'University Level')->first()->id;
        $deptLevelId = DB::table('meta_types')->where('category', 'award_level')->where('name', 'Department Level')->first()->id;
        $natLevelId = DB::table('meta_types')->where('category', 'award_level')->where('name', 'National Level')->first()->id;
        
        $awards = [
            [
                'name' => 'Outstanding Teacher Award',
                'description' => 'Award for excellence in teaching',
                'level_id' => $uniLevelId
            ],
            [
                'name' => 'Research Excellence Award',
                'description' => 'Award for outstanding research contributions',
                'level_id' => $uniLevelId
            ],
            [
                'name' => 'Department Service Award',
                'description' => 'Recognition for exceptional service to the department',
                'level_id' => $deptLevelId
            ],
            [
                'name' => 'Innovation in Teaching Award',
                'description' => 'Award for innovative teaching methods',
                'level_id' => $deptLevelId
            ],
            [
                'name' => 'National Educator Award',
                'description' => 'Prestigious national recognition for educators',
                'level_id' => $natLevelId
            ],
        ];
        
        foreach ($awards as $award) {
            DB::table('awards')->insert([
                'name' => $award['name'],
                'description' => $award['description'],
                'level_id' => $award['level_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 