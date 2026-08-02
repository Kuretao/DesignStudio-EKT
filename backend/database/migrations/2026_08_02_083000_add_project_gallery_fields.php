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
            if (! Schema::hasColumn('projects', 'gallery_images')) {
                $table->text('gallery_images')->nullable()->after('featured_image_file');
            }

            if (! Schema::hasColumn('projects', 'gallery_labels_ru')) {
                $table->text('gallery_labels_ru')->nullable()->after('gallery_images');
            }

            if (! Schema::hasColumn('projects', 'gallery_labels_en')) {
                $table->text('gallery_labels_en')->nullable()->after('gallery_labels_ru');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            foreach (['gallery_labels_en', 'gallery_labels_ru', 'gallery_images'] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
