<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            MetaTypesTableSeeder::class,
            UnitsTableSeeder::class,
            UsersTableSeeder::class,
            UnitHeadsTableSeeder::class,
            EvaluationCriteriaTableSeeder::class,
            AwardsTableSeeder::class,
            EvaluationsTableSeeder::class,
            EvaluationDetailsTableSeeder::class,
            AwardNominationsTableSeeder::class,
            DocumentsTableSeeder::class,
            AuditLogsTableSeeder::class,
        ]);
    }
} 