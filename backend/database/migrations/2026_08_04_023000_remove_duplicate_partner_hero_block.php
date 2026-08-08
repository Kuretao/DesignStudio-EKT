<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('slug', 'partneram')->value('id');

        if (! $pageId) {
            return;
        }

        $heroCount = DB::table('page_blocks')
            ->where('page_id', $pageId)
            ->where('type', 'hero')
            ->count();

        if ($heroCount <= 1) {
            return;
        }

        DB::table('page_blocks')
            ->where('page_id', $pageId)
            ->where('type', 'hero')
            ->where('position', 10)
            ->where('title_ru', 'Партнерам')
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
