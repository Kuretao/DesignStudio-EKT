<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('projects', 'hero_images')) {
            Schema::table('projects', static function (Blueprint $table): void {
                $table->text('hero_images')->nullable()->after('image');
            });
        }

        if (! Schema::hasColumn('projects', 'case_intro_ru')) {
            Schema::table('projects', static function (Blueprint $table): void {
                $table->text('case_intro_ru')->nullable()->after('deliverables_en');
            });
        }

        if (! Schema::hasColumn('projects', 'case_intro_en')) {
            Schema::table('projects', static function (Blueprint $table): void {
                $table->text('case_intro_en')->nullable()->after('case_intro_ru');
            });
        }

        if (! Schema::hasColumn('projects', 'is_selected')) {
            Schema::table('projects', static function (Blueprint $table): void {
                $table->boolean('is_selected')->default(false)->after('is_featured')->index();
            });
        }

        foreach ([
            'selected_task_title_ru' => 'string',
            'selected_task_title_en' => 'string',
            'selected_task_text_ru' => 'text',
            'selected_task_text_en' => 'text',
            'selected_result_title_ru' => 'string',
            'selected_result_title_en' => 'string',
            'selected_result_text_ru' => 'text',
            'selected_result_text_en' => 'text',
            'selected_format_title_ru' => 'string',
            'selected_format_title_en' => 'string',
            'selected_format_text_ru' => 'text',
            'selected_format_text_en' => 'text',
        ] as $column => $type) {
            if (! Schema::hasColumn('projects', $column)) {
                Schema::table('projects', static function (Blueprint $table) use ($column, $type): void {
                    $type === 'string'
                        ? $table->string($column)->nullable()->after('is_selected')
                        : $table->text($column)->nullable()->after('is_selected');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('projects', static function (Blueprint $table): void {
            foreach ([
                'hero_images',
                'case_intro_ru',
                'case_intro_en',
                'is_selected',
                'selected_task_title_ru',
                'selected_task_title_en',
                'selected_task_text_ru',
                'selected_task_text_en',
                'selected_result_title_ru',
                'selected_result_title_en',
                'selected_result_text_ru',
                'selected_result_text_en',
                'selected_format_title_ru',
                'selected_format_title_en',
                'selected_format_text_ru',
                'selected_format_text_en',
            ] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
