<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_blocks', function (Blueprint $table): void {
            if (! Schema::hasColumn('page_blocks', 'visual_variant')) {
                $table->string('visual_variant')->default('default')->after('type');
            }

            if (! Schema::hasColumn('page_blocks', 'media_position')) {
                $table->string('media_position')->nullable()->after('visual_variant');
            }

            if (! Schema::hasColumn('page_blocks', 'motion_preset')) {
                $table->string('motion_preset')->default('motion')->after('media_position');
            }

            if (! Schema::hasColumn('page_blocks', 'card_state')) {
                $table->string('card_state')->default('normal')->after('motion_preset');
            }

            if (! Schema::hasColumn('page_blocks', 'image_alt')) {
                $table->string('image_alt')->nullable()->after('image');
            }

            if (! Schema::hasColumn('page_blocks', 'image_alt_ru')) {
                $table->string('image_alt_ru')->nullable()->after('image_alt');
            }

            if (! Schema::hasColumn('page_blocks', 'image_alt_en')) {
                $table->string('image_alt_en')->nullable()->after('image_alt_ru');
            }

            if (! Schema::hasColumn('page_blocks', 'settings')) {
                $table->json('settings')->nullable()->after('link_href');
            }
        });
    }

    public function down(): void
    {
        Schema::table('page_blocks', function (Blueprint $table): void {
            $drop = [];

            foreach ([
                'visual_variant',
                'media_position',
                'motion_preset',
                'card_state',
                'image_alt',
                'image_alt_ru',
                'image_alt_en',
                'settings',
            ] as $column) {
                if (Schema::hasColumn('page_blocks', $column)) {
                    $drop[] = $column;
                }
            }

            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
