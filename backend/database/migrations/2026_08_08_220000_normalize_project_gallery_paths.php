<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pairs = [
            ['image_file', 'image', 'projects/'],
            ['before_image_file', 'before_image', 'projects/'],
            ['after_image_file', 'after_image', 'projects/'],
            ['featured_image_file', 'featured_image', 'projects/featured/'],
            ['featured_gallery_image_1_file', 'featured_gallery_image_1', 'projects/featured/'],
            ['featured_gallery_image_2_file', 'featured_gallery_image_2', 'projects/featured/'],
            ['featured_gallery_image_3_file', 'featured_gallery_image_3', 'projects/featured/'],
        ];

        DB::table('projects')
            ->select(['id', ...array_unique(array_merge(...array_map(
                static fn (array $pair): array => [$pair[0], $pair[1]],
                $pairs,
            )))])
            ->orderBy('id')
            ->chunkById(100, static function ($projects) use ($pairs): void {
                foreach ($projects as $project) {
                    $updates = [];

                    foreach ($pairs as [$fileField, $urlField, $uploadDirectory]) {
                        $value = trim((string) ($project->{$fileField} ?? ''));

                        if ($value === '' || str_starts_with($value, $uploadDirectory)) {
                            continue;
                        }

                        $updates[$urlField] = $value;
                        $updates[$fileField] = null;
                    }

                    if ($updates !== []) {
                        DB::table('projects')->where('id', $project->id)->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        // Data normalization is intentionally irreversible.
    }
};
