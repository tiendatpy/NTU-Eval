<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của các loại đơn vị
        $departmentTypeId = DB::table('meta_types')->where('category', 'unit_type')->where('name', 'Department')->first()->id;
        $facultyTypeId = DB::table('meta_types')->where('category', 'unit_type')->where('name', 'Faculty')->first()->id;
        $divisionTypeId = DB::table('meta_types')->where('category', 'unit_type')->where('name', 'Division')->first()->id;
        
        // Tạo các đơn vị cấp cao
        $units = [
            [
                'name' => 'Faculty of Information Technology',
                'type_id' => $facultyTypeId,
                'parent_id' => null
            ],
            [
                'name' => 'Faculty of Business Administration',
                'type_id' => $facultyTypeId,
                'parent_id' => null
            ],
            [
                'name' => 'Faculty of Engineering',
                'type_id' => $facultyTypeId,
                'parent_id' => null
            ],
        ];
        
        foreach ($units as $unit) {
            DB::table('units')->insert([
                'name' => $unit['name'],
                'type_id' => $unit['type_id'],
                'parent_id' => $unit['parent_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Tạo các đơn vị con
        $itFacultyId = DB::table('units')->where('name', 'Faculty of Information Technology')->first()->id;
        $bbaFacultyId = DB::table('units')->where('name', 'Faculty of Business Administration')->first()->id;
        $engFacultyId = DB::table('units')->where('name', 'Faculty of Engineering')->first()->id;
        
        $subUnits = [
            [
                'name' => 'Department of Software Engineering',
                'type_id' => $departmentTypeId,
                'parent_id' => $itFacultyId
            ],
            [
                'name' => 'Department of Computer Networks',
                'type_id' => $departmentTypeId,
                'parent_id' => $itFacultyId
            ],
            [
                'name' => 'Department of Marketing',
                'type_id' => $departmentTypeId,
                'parent_id' => $bbaFacultyId
            ],
            [
                'name' => 'Department of Finance',
                'type_id' => $departmentTypeId,
                'parent_id' => $bbaFacultyId
            ],
            [
                'name' => 'Department of Mechanical Engineering',
                'type_id' => $departmentTypeId,
                'parent_id' => $engFacultyId
            ],
            [
                'name' => 'Department of Civil Engineering',
                'type_id' => $departmentTypeId,
                'parent_id' => $engFacultyId
            ],
            [
                'name' => 'IT Support Division',
                'type_id' => $divisionTypeId,
                'parent_id' => $itFacultyId
            ],
        ];
        
        foreach ($subUnits as $unit) {
            DB::table('units')->insert([
                'name' => $unit['name'],
                'type_id' => $unit['type_id'],
                'parent_id' => $unit['parent_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 