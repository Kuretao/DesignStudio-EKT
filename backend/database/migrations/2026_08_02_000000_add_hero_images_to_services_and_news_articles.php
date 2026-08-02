<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            if (! Schema::hasColumn('services', 'hero_images')) {
                $table->text('hero_images')->nullable()->after('image_file');
            }
        });

        Schema::table('news_articles', function (Blueprint $table): void {
            if (! Schema::hasColumn('news_articles', 'hero_images')) {
                $table->text('hero_images')->nullable()->after('image_file');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            if (Schema::hasColumn('services', 'hero_images')) {
                $table->dropColumn('hero_images');
            }
        });

        Schema::table('news_articles', function (Blueprint $table): void {
            if (Schema::hasColumn('news_articles', 'hero_images')) {
                $table->dropColumn('hero_images');
            }
        });
    }
};
