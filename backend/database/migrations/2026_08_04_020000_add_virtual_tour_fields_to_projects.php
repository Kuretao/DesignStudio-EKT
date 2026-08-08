<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addBoolean('is_virtual_tour', 'is_selected');
        $this->addString('virtual_tour_eyebrow_ru', 'is_virtual_tour');
        $this->addString('virtual_tour_eyebrow_en', 'virtual_tour_eyebrow_ru');
        $this->addString('virtual_tour_title_ru', 'virtual_tour_eyebrow_en');
        $this->addString('virtual_tour_title_en', 'virtual_tour_title_ru');
        $this->addText('virtual_tour_text_ru', 'virtual_tour_title_en');
        $this->addText('virtual_tour_text_en', 'virtual_tour_text_ru');
        $this->addString('virtual_tour_button_ru', 'virtual_tour_text_en');
        $this->addString('virtual_tour_button_en', 'virtual_tour_button_ru');
        $this->addJson('virtual_tour_scenes', 'virtual_tour_button_en');
    }

    public function down(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            foreach ([
                'is_virtual_tour',
                'virtual_tour_eyebrow_ru',
                'virtual_tour_eyebrow_en',
                'virtual_tour_title_ru',
                'virtual_tour_title_en',
                'virtual_tour_text_ru',
                'virtual_tour_text_en',
                'virtual_tour_button_ru',
                'virtual_tour_button_en',
                'virtual_tour_scenes',
            ] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function addBoolean(string $column, string $after): void
    {
        if (Schema::hasColumn('projects', $column)) {
            return;
        }

        Schema::table('projects', static function (Blueprint $table) use ($column, $after): void {
            $table->boolean($column)->default(false)->after($after)->index();
        });
    }

    private function addString(string $column, string $after): void
    {
        if (Schema::hasColumn('projects', $column)) {
            return;
        }

        Schema::table('projects', static function (Blueprint $table) use ($column, $after): void {
            $table->string($column)->nullable()->after($after);
        });
    }

    private function addText(string $column, string $after): void
    {
        if (Schema::hasColumn('projects', $column)) {
            return;
        }

        Schema::table('projects', static function (Blueprint $table) use ($column, $after): void {
            $table->text($column)->nullable()->after($after);
        });
    }

    private function addJson(string $column, string $after): void
    {
        if (Schema::hasColumn('projects', $column)) {
            return;
        }

        Schema::table('projects', static function (Blueprint $table) use ($column, $after): void {
            $table->json($column)->nullable()->after($after);
        });
    }
};
