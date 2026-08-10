<?php

use App\Support\DefaultLegalPages;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach (DefaultLegalPages::pages() as $slug => $content) {
            $page = DB::table('pages')->where('slug', $slug)->first();

            if (! $page) {
                DB::table('pages')->insert([
                    'slug' => $slug,
                    'title' => $content['title_ru'],
                    'title_ru' => $content['title_ru'],
                    'title_en' => $content['title_en'],
                    'template' => 'legal',
                    'body' => $content['body_ru'],
                    'body_ru' => $content['body_ru'],
                    'is_published' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                continue;
            }

            $updates = ['template' => 'legal'];

            if (blank($page->title ?? null)) {
                $updates['title'] = $content['title_ru'];
            }

            if (blank($page->title_ru ?? null)) {
                $updates['title_ru'] = $content['title_ru'];
            }

            if (blank($page->title_en ?? null)) {
                $updates['title_en'] = $content['title_en'];
            }

            if (blank($page->body ?? null)) {
                $updates['body'] = $content['body_ru'];
            }

            if (blank($page->body_ru ?? null)) {
                $updates['body_ru'] = $content['body_ru'];
            }

            if (count($updates) > 1) {
                $updates['updated_at'] = $now;
            }

            DB::table('pages')->where('id', $page->id)->update($updates);
        }
    }

    public function down(): void
    {
        // Legal content can be edited after deployment, so rollback must not erase it.
    }
};
