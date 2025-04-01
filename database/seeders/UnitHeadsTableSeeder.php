<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitHeadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy thông tin người dùng và đơn vị
        $itDean = DB::table('users')->where('username', 'itdean')->first();
        $seHead = DB::table('users')->where('username', 'sehead')->first();
        $cnHead = DB::table('users')->where('username', 'cnhead')->first();
        
        $itFaculty = DB::table('units')->where('name', 'Faculty of Information Technology')->first();
        $seDept = DB::table('units')->where('name', 'Department of Software Engineering')->first();
        $cnDept = DB::table('units')->where('name', 'Department of Computer Networks')->first();
        
        $unitHeads = [
            [
                'unit_id' => $itFaculty->id,
                'head_id' => $itDean->id
            ],
            [
                'unit_id' => $seDept->id,
                'head_id' => $seHead->id
            ],
            [
                'unit_id' => $cnDept->id,
                'head_id' => $cnHead->id
            ],
        ];
        
        foreach ($unitHeads as $head) {
            DB::table('unit_heads')->insert([
                'unit_id' => $head['unit_id'],
                'head_id' => $head['head_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 