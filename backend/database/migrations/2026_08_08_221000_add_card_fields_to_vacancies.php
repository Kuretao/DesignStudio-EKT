<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacancies', function (Blueprint $table): void {
            $table->string('department')->nullable()->after('employment_en');
            $table->string('department_ru')->nullable()->after('department');
            $table->string('department_en')->nullable()->after('department_ru');
            $table->string('format')->nullable()->after('department_en');
            $table->string('format_ru')->nullable()->after('format');
            $table->string('format_en')->nullable()->after('format_ru');
            $table->string('experience')->nullable()->after('format_en');
            $table->string('experience_ru')->nullable()->after('experience');
            $table->string('experience_en')->nullable()->after('experience_ru');
            $table->longText('perks')->nullable()->after('responsibilities_en');
            $table->longText('perks_ru')->nullable()->after('perks');
            $table->longText('perks_en')->nullable()->after('perks_ru');
            $table->string('image_file')->nullable()->after('perks_en');
            $table->text('image')->nullable()->after('image_file');
        });

        DB::table('vacancies')->update([
            'department' => DB::raw('employment'),
            'department_ru' => DB::raw('employment_ru'),
            'department_en' => DB::raw('employment_en'),
            'format' => DB::raw('employment'),
            'format_ru' => DB::raw('employment_ru'),
            'format_en' => DB::raw('employment_en'),
        ]);
    }

    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table): void {
            $table->dropColumn([
                'department', 'department_ru', 'department_en',
                'format', 'format_ru', 'format_en',
                'experience', 'experience_ru', 'experience_en',
                'perks', 'perks_ru', 'perks_en',
                'image_file', 'image',
            ]);
        });
    }
};
