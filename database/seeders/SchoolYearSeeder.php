<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        SchoolYear::updateOrCreate(
            ['name' => '2026-2027'],
            [
                'year' => '2026-2027',
                'is_active' => true,
                'is_enrollment_open' => true,
            ]
        );
    }
}
