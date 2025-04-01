<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'Admin', 'description' => 'Quản trị viên hệ thống'],
            ['name' => 'Manager', 'description' => 'Quản lý đơn vị'],
            ['name' => 'Staff', 'description' => 'Nhân viên'],
            ['name' => 'Evaluator', 'description' => 'Người đánh giá'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 