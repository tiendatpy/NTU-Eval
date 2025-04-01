<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AwardNominationsTableSeeder extends Seeder
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
        
        // Lấy ID của đơn vị
        $seDept = DB::table('units')->where('name', 'Department of Software Engineering')->first();
        $cnDept = DB::table('units')->where('name', 'Department of Computer Networks')->first();
        
        // Lấy ID của giải thưởng
        $teachingAward = DB::table('awards')->where('name', 'Outstanding Teacher Award')->first();
        $researchAward = DB::table('awards')->where('name', 'Research Excellence Award')->first();
        $serviceAward = DB::table('awards')->where('name', 'Department Service Award')->first();
        
        // Lấy ID của trạng thái
        $pendingStatusId = DB::table('meta_types')->where('category', 'nomination_status')->where('name', 'Pending')->first()->id;
        $approvedStatusId = DB::table('meta_types')->where('category', 'nomination_status')->where('name', 'Approved')->first()->id;
        $rejectedStatusId = DB::table('meta_types')->where('category', 'nomination_status')->where('name', 'Rejected')->first()->id;
        
        $nominations = [
            [
                'user_id' => $staff1->id,
                'unit_id' => $seDept->id,
                'award_id' => $teachingAward->id,
                'period' => 2023,
                'status_id' => $approvedStatusId
            ],
            [
                'user_id' => $staff2->id,
                'unit_id' => $cnDept->id,
                'award_id' => $researchAward->id,
                'period' => 2023,
                'status_id' => $pendingStatusId
            ],
            [
                'user_id' => $staff1->id,
                'unit_id' => $seDept->id,
                'award_id' => $serviceAward->id,
                'period' => 2022,
                'status_id' => $rejectedStatusId
            ],
        ];
        
        foreach ($nominations as $nomination) {
            DB::table('award_nominations')->insert([
                'user_id' => $nomination['user_id'],
                'unit_id' => $nomination['unit_id'],
                'award_id' => $nomination['award_id'],
                'period' => $nomination['period'],
                'status_id' => $nomination['status_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 