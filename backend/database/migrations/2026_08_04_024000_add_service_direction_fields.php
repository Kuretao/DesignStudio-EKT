<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('menu_items', 'image_file')) {
                $table->string('image_file')->nullable()->after('description_en');
            }

            if (! Schema::hasColumn('menu_items', 'image')) {
                $table->text('image')->nullable()->after('image_file');
            }

            if (! Schema::hasColumn('menu_items', 'image_alt_ru')) {
                $table->string('image_alt_ru')->nullable()->after('image');
            }

            if (! Schema::hasColumn('menu_items', 'image_alt_en')) {
                $table->string('image_alt_en')->nullable()->after('image_alt_ru');
            }
        });

        Schema::table('services', function (Blueprint $table): void {
            if (! Schema::hasColumn('services', 'service_direction_id')) {
                $table->foreignId('service_direction_id')
                    ->nullable()
                    ->after('slug')
                    ->constrained('menu_items')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            if (Schema::hasColumn('services', 'service_direction_id')) {
                $table->dropConstrainedForeignId('service_direction_id');
            }
        });

        Schema::table('menu_items', function (Blueprint $table): void {
            foreach (['image_file', 'image', 'image_alt_ru', 'image_alt_en'] as $column) {
                if (Schema::hasColumn('menu_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
