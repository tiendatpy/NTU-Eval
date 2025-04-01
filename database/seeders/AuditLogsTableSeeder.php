<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của người dùng
        $admin = DB::table('users')->where('username', 'admin')->first();
        $itDean = DB::table('users')->where('username', 'itdean')->first();
        $seHead = DB::table('users')->where('username', 'sehead')->first();
        
        // Lấy ID của các bản ghi từ các bảng khác nhau
        $evaluation = DB::table('evaluations')->first();
        $award = DB::table('awards')->first();
        $user = DB::table('users')->where('username', 'staff1')->first();
        $document = DB::table('documents')->first();
        
        $auditLogs = [
            [
                'user_id' => $admin->id,
                'action' => 'CREATE',
                'target_table' => 'users',
                'target_id' => $user->id
            ],
            [
                'user_id' => $itDean->id,
                'action' => 'UPDATE',
                'target_table' => 'evaluations',
                'target_id' => $evaluation->id
            ],
            [
                'user_id' => $seHead->id,
                'action' => 'VIEW',
                'target_table' => 'awards',
                'target_id' => $award->id
            ],
            [
                'user_id' => $admin->id,
                'action' => 'UPLOAD',
                'target_table' => 'documents',
                'target_id' => $document->id
            ],
            [
                'user_id' => $itDean->id,
                'action' => 'APPROVE',
                'target_table' => 'evaluations',
                'target_id' => $evaluation->id
            ],
            [
                'user_id' => $admin->id,
                'action' => 'LOGIN',
                'target_table' => 'users',
                'target_id' => $admin->id
            ],
            [
                'user_id' => $seHead->id,
                'action' => 'SUBMIT',
                'target_table' => 'award_nominations',
                'target_id' => 1
            ],
        ];
        
        foreach ($auditLogs as $log) {
            DB::table('audit_logs')->insert([
                'user_id' => $log['user_id'],
                'action' => $log['action'],
                'target_table' => $log['target_table'],
                'target_id' => $log['target_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 