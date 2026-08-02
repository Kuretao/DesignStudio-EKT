<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'gallery_eyebrow_ru')) {
                $table->string('gallery_eyebrow_ru')->nullable()->after('gallery_labels_en');
            }

            if (! Schema::hasColumn('projects', 'gallery_eyebrow_en')) {
                $table->string('gallery_eyebrow_en')->nullable()->after('gallery_eyebrow_ru');
            }

            if (! Schema::hasColumn('projects', 'gallery_title_ru')) {
                $table->string('gallery_title_ru')->nullable()->after('gallery_eyebrow_en');
            }

            if (! Schema::hasColumn('projects', 'gallery_title_en')) {
                $table->string('gallery_title_en')->nullable()->after('gallery_title_ru');
            }

            if (! Schema::hasColumn('projects', 'gallery_text_ru')) {
                $table->text('gallery_text_ru')->nullable()->after('gallery_title_en');
            }

            if (! Schema::hasColumn('projects', 'gallery_text_en')) {
                $table->text('gallery_text_en')->nullable()->after('gallery_text_ru');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            foreach ([
                'gallery_text_en',
                'gallery_text_ru',
                'gallery_title_en',
                'gallery_title_ru',
                'gallery_eyebrow_en',
                'gallery_eyebrow_ru',
            ] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
