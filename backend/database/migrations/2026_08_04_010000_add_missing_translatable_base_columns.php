<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addColumnIfMissing('services', 'pdf_title', 'string', 'pdf_title_en');

        $this->addColumnIfMissing('projects', 'featured_label', 'string', 'is_featured');
        $this->addColumnIfMissing('projects', 'featured_title', 'string', 'featured_label');
        $this->addColumnIfMissing('projects', 'featured_description', 'text', 'featured_title');

        DB::table('services')
            ->whereNull('pdf_title')
            ->whereNotNull('pdf_title_ru')
            ->update(['pdf_title' => DB::raw('pdf_title_ru')]);

        DB::table('projects')
            ->whereNull('featured_label')
            ->whereNotNull('featured_label_ru')
            ->update(['featured_label' => DB::raw('featured_label_ru')]);

        DB::table('projects')
            ->whereNull('featured_title')
            ->whereNotNull('featured_title_ru')
            ->update(['featured_title' => DB::raw('featured_title_ru')]);

        DB::table('projects')
            ->whereNull('featured_description')
            ->whereNotNull('featured_description_ru')
            ->update(['featured_description' => DB::raw('featured_description_ru')]);
    }

    public function down(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            foreach (['featured_label', 'featured_title', 'featured_description'] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('services', static function (Blueprint $table): void {
            if (Schema::hasColumn('services', 'pdf_title')) {
                $table->dropColumn('pdf_title');
            }
        });
    }

    private function addColumnIfMissing(string $tableName, string $column, string $type, string $after): void
    {
        if (Schema::hasColumn($tableName, $column)) {
            return;
        }

        Schema::table($tableName, static function (Blueprint $table) use ($column, $type, $after): void {
            $definition = $type === 'text'
                ? $table->text($column)
                : $table->string($column);

            $definition->nullable()->after($after);
        });
    }
};
