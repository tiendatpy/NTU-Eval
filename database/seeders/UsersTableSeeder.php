<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của các vai trò
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->first()->id;
        $managerRoleId = DB::table('roles')->where('name', 'Manager')->first()->id;
        $staffRoleId = DB::table('roles')->where('name', 'Staff')->first()->id;
        $evaluatorRoleId = DB::table('roles')->where('name', 'Evaluator')->first()->id;
        
        // Lấy ID của các đơn vị
        $itFacultyId = DB::table('units')->where('name', 'Faculty of Information Technology')->first()->id;
        $seDeptId = DB::table('units')->where('name', 'Department of Software Engineering')->first()->id;
        $cnDeptId = DB::table('units')->where('name', 'Department of Computer Networks')->first()->id;
        
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'full_name' => 'System Administrator',
                'email' => 'admin@example.com',
                'phone' => '0901234567',
                'role_id' => $adminRoleId,
                'unit_id' => null
            ],
            [
                'username' => 'itdean',
                'password' => Hash::make('password'),
                'full_name' => 'IT Faculty Dean',
                'email' => 'itdean@example.com',
                'phone' => '0901234568',
                'role_id' => $managerRoleId,
                'unit_id' => $itFacultyId
            ],
            [
                'username' => 'sehead',
                'password' => Hash::make('password'),
                'full_name' => 'SE Department Head',
                'email' => 'sehead@example.com',
                'phone' => '0901234569',
                'role_id' => $managerRoleId,
                'unit_id' => $seDeptId
            ],
            [
                'username' => 'cnhead',
                'password' => Hash::make('password'),
                'full_name' => 'CN Department Head',
                'email' => 'cnhead@example.com',
                'phone' => '0901234570',
                'role_id' => $managerRoleId,
                'unit_id' => $cnDeptId
            ],
            [
                'username' => 'staff1',
                'password' => Hash::make('password'),
                'full_name' => 'Staff Member 1',
                'email' => 'staff1@example.com',
                'phone' => '0901234571',
                'role_id' => $staffRoleId,
                'unit_id' => $seDeptId
            ],
            [
                'username' => 'staff2',
                'password' => Hash::make('password'),
                'full_name' => 'Staff Member 2',
                'email' => 'staff2@example.com',
                'phone' => '0901234572',
                'role_id' => $staffRoleId,
                'unit_id' => $cnDeptId
            ],
            [
                'username' => 'evaluator1',
                'password' => Hash::make('password'),
                'full_name' => 'Evaluator 1',
                'email' => 'evaluator1@example.com',
                'phone' => '0901234573',
                'role_id' => $evaluatorRoleId,
                'unit_id' => $itFacultyId
            ],
        ];
        
        foreach ($users as $user) {
            DB::table('users')->insert([
                'username' => $user['username'],
                'password' => $user['password'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role_id' => $user['role_id'],
                'unit_id' => $user['unit_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 