<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            if (! Schema::hasColumn('services', 'compare_eyebrow')) {
                $table->string('compare_eyebrow')->nullable()->after('hero_images');
                $table->string('compare_eyebrow_ru')->nullable()->after('compare_eyebrow');
                $table->string('compare_eyebrow_en')->nullable()->after('compare_eyebrow_ru');
            }

            if (! Schema::hasColumn('services', 'compare_title')) {
                $table->string('compare_title')->nullable()->after('compare_eyebrow_en');
                $table->string('compare_title_ru')->nullable()->after('compare_title');
                $table->string('compare_title_en')->nullable()->after('compare_title_ru');
            }

            if (! Schema::hasColumn('services', 'compare_text')) {
                $table->text('compare_text')->nullable()->after('compare_title_en');
                $table->text('compare_text_ru')->nullable()->after('compare_text');
                $table->text('compare_text_en')->nullable()->after('compare_text_ru');
            }

            if (! Schema::hasColumn('services', 'compare_before_image_file')) {
                $table->string('compare_before_image_file')->nullable()->after('compare_text_en');
                $table->text('compare_before_image')->nullable()->after('compare_before_image_file');
            }

            if (! Schema::hasColumn('services', 'compare_after_image_file')) {
                $table->string('compare_after_image_file')->nullable()->after('compare_before_image');
                $table->text('compare_after_image')->nullable()->after('compare_after_image_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $columns = [
                'compare_eyebrow',
                'compare_eyebrow_ru',
                'compare_eyebrow_en',
                'compare_title',
                'compare_title_ru',
                'compare_title_en',
                'compare_text',
                'compare_text_ru',
                'compare_text_en',
                'compare_before_image_file',
                'compare_before_image',
                'compare_after_image_file',
                'compare_after_image',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
