<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $deletedPaths = [
            'cms/kit-final-20260803074652-z42v3d.webp',
            'projects/kDBjjcNqAMX4og6363F8mLgqaWW3TZPTGFYWGN2m.webp',
        ];

        DB::table('projects')
            ->select(['id', 'hero_images', 'gallery_images', 'featured_gallery_images'])
            ->orderBy('id')
            ->chunkById(100, static function ($projects) use ($deletedPaths): void {
                foreach ($projects as $project) {
                    $updates = [];

                    foreach (['hero_images', 'gallery_images', 'featured_gallery_images'] as $field) {
                        $lines = preg_split('/\R/u', (string) ($project->{$field} ?? '')) ?: [];
                        $clean = collect($lines)
                            ->map(static fn (string $line): string => trim($line))
                            ->filter(static fn (string $line): bool => $line !== '' && ! in_array($line, $deletedPaths, true))
                            ->values()
                            ->implode("\n");

                        if ($clean !== trim((string) ($project->{$field} ?? ''))) {
                            $updates[$field] = $clean !== '' ? $clean : null;
                        }
                    }

                    if ($updates !== []) {
                        $updates['updated_at'] = now();
                        DB::table('projects')->where('id', $project->id)->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        // Deleted media references must not be restored.
    }
};
