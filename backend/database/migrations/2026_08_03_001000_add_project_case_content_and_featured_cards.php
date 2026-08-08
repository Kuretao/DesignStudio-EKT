<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'featured_gallery_image_1_file')) {
                $table->string('featured_gallery_image_1_file')->nullable()->after('featured_gallery_images');
            }

            if (! Schema::hasColumn('projects', 'featured_gallery_image_1')) {
                $table->text('featured_gallery_image_1')->nullable()->after('featured_gallery_image_1_file');
            }

            if (! Schema::hasColumn('projects', 'featured_gallery_image_2_file')) {
                $table->string('featured_gallery_image_2_file')->nullable()->after('featured_gallery_image_1');
            }

            if (! Schema::hasColumn('projects', 'featured_gallery_image_2')) {
                $table->text('featured_gallery_image_2')->nullable()->after('featured_gallery_image_2_file');
            }

            if (! Schema::hasColumn('projects', 'featured_gallery_image_3_file')) {
                $table->string('featured_gallery_image_3_file')->nullable()->after('featured_gallery_image_2');
            }

            if (! Schema::hasColumn('projects', 'featured_gallery_image_3')) {
                $table->text('featured_gallery_image_3')->nullable()->after('featured_gallery_image_3_file');
            }

            if (! Schema::hasColumn('projects', 'story_chapters')) {
                $table->json('story_chapters')->nullable()->after('gallery_labels_en');
            }

            if (! Schema::hasColumn('projects', 'deliverables_ru')) {
                $table->text('deliverables_ru')->nullable()->after('story_chapters');
            }

            if (! Schema::hasColumn('projects', 'deliverables_en')) {
                $table->text('deliverables_en')->nullable()->after('deliverables_ru');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            foreach ([
                'featured_gallery_image_1_file',
                'featured_gallery_image_1',
                'featured_gallery_image_2_file',
                'featured_gallery_image_2',
                'featured_gallery_image_3_file',
                'featured_gallery_image_3',
                'story_chapters',
                'deliverables_ru',
                'deliverables_en',
            ] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
