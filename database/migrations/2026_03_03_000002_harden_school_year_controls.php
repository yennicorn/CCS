<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_years', function (Blueprint $table) {
            if (!Schema::hasColumn('school_years', 'year')) {
                $table->string('year', 20)->nullable()->after('name');
            }

            if (!Schema::hasColumn('school_years', 'is_enrollment_open')) {
                $table->boolean('is_enrollment_open')->default(false)->after('is_active');
            }

            if (!Schema::hasColumn('school_years', 'enrollment_start_at')) {
                $table->timestamp('enrollment_start_at')->nullable()->after('is_enrollment_open');
            }

            if (!Schema::hasColumn('school_years', 'enrollment_end_at')) {
                $table->timestamp('enrollment_end_at')->nullable()->after('enrollment_start_at');
            }
        });

        DB::table('school_years')->orderBy('id')->chunkById(100, function ($rows) {
            foreach ($rows as $row) {
                DB::table('school_years')
                    ->where('id', $row->id)
                    ->update([
                        'year' => $row->year ?? $row->name,
                        'is_enrollment_open' => isset($row->is_enrollment_open)
                            ? (bool) $row->is_enrollment_open
                            : (bool) ($row->enrollment_open ?? false),
                    ]);
            }
        });

        Schema::table('school_years', function (Blueprint $table) {
            $table->index(['is_active', 'is_enrollment_open'], 'school_years_active_enrollment_idx');
            $table->unique('year', 'school_years_year_unique');
        });
    }

    public function down(): void
    {
        Schema::table('school_years', function (Blueprint $table) {
            $table->dropIndex('school_years_active_enrollment_idx');
            $table->dropUnique('school_years_year_unique');

            if (Schema::hasColumn('school_years', 'enrollment_end_at')) {
                $table->dropColumn('enrollment_end_at');
            }
            if (Schema::hasColumn('school_years', 'enrollment_start_at')) {
                $table->dropColumn('enrollment_start_at');
            }
            if (Schema::hasColumn('school_years', 'is_enrollment_open')) {
                $table->dropColumn('is_enrollment_open');
            }
            if (Schema::hasColumn('school_years', 'year')) {
                $table->dropColumn('year');
            }
        });
    }
};
