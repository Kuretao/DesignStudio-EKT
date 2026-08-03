<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'featured_gallery_images')) {
                $table->text('featured_gallery_images')->nullable()->after('featured_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            if (Schema::hasColumn('projects', 'featured_gallery_images')) {
                $table->dropColumn('featured_gallery_images');
            }
        });
    }
};
