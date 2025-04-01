<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của loại tài liệu
        $evalFormTypeId = DB::table('meta_types')->where('category', 'document_type')->where('name', 'Evaluation Form')->first()->id;
        $certTypeId = DB::table('meta_types')->where('category', 'document_type')->where('name', 'Certificate')->first()->id;
        $reportTypeId = DB::table('meta_types')->where('category', 'document_type')->where('name', 'Report')->first()->id;
        
        // Lấy ID của người dùng
        $admin = DB::table('users')->where('username', 'admin')->first();
        $itDean = DB::table('users')->where('username', 'itdean')->first();
        $seHead = DB::table('users')->where('username', 'sehead')->first();
        
        $documents = [
            [
                'type_id' => $evalFormTypeId,
                'file_name' => 'evaluation_form_2023.pdf',
                'file_path' => 'documents/evaluation_forms/evaluation_form_2023.pdf',
                'uploaded_by' => $admin->id
            ],
            [
                'type_id' => $certTypeId,
                'file_name' => 'teaching_excellence_certificate.pdf',
                'file_path' => 'documents/certificates/teaching_excellence_certificate.pdf',
                'uploaded_by' => $itDean->id
            ],
            [
                'type_id' => $reportTypeId,
                'file_name' => 'annual_evaluation_report_2023.pdf',
                'file_path' => 'documents/reports/annual_evaluation_report_2023.pdf',
                'uploaded_by' => $seHead->id
            ],
            [
                'type_id' => $evalFormTypeId,
                'file_name' => 'evaluation_form_2022.pdf',
                'file_path' => 'documents/evaluation_forms/evaluation_form_2022.pdf',
                'uploaded_by' => $admin->id
            ],
            [
                'type_id' => $reportTypeId,
                'file_name' => 'department_performance_report.pdf',
                'file_path' => 'documents/reports/department_performance_report.pdf',
                'uploaded_by' => $seHead->id
            ],
        ];
        
        foreach ($documents as $document) {
            DB::table('documents')->insert([
                'type_id' => $document['type_id'],
                'file_name' => $document['file_name'],
                'file_path' => $document['file_path'],
                'uploaded_by' => $document['uploaded_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 