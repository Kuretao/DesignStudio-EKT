<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $position = (int) DB::table('ui_texts')->max('position');

        foreach ([
            'portfolio.loadingTitle' => [
                'group' => 'portfolio',
                'label' => 'Портфолио: загрузка заголовок',
                'ru' => 'Загружаем проекты',
                'en' => 'Loading projects',
            ],
            'portfolio.loadingText' => [
                'group' => 'portfolio',
                'label' => 'Портфолио: загрузка текст',
                'ru' => 'Подтягиваем актуальные проекты.',
                'en' => 'Loading current projects.',
            ],
            'portfolio.emptyProjects' => [
                'group' => 'portfolio',
                'label' => 'Портфолио: пустой список',
                'ru' => 'По выбранным фильтрам проектов не найдено.',
                'en' => 'No projects were found for the selected filters.',
            ],
            'serviceDetail.portfolio.text' => [
                'group' => 'service-detail',
                'label' => 'Портфолио услуги: текст',
                'ru' => 'Карточки ведут на индивидуальные страницы проектов с уникальным URL. Здесь собраны релевантные кейсы по выбранному направлению.',
                'en' => 'Cards open individual project pages with unique URLs. Relevant cases for the selected service are collected here.',
            ],
            'quiz.intro' => [
                'group' => 'quiz',
                'label' => 'Квиз: вводный текст',
                'ru' => 'Ответьте на 5 вопросов, и мы подготовим персональное предложение. Бонусом откроем PDF по выбранной услуге, если он доступен.',
                'en' => 'Answer 5 questions and we will prepare a personal proposal. As a bonus, we will open the PDF for the selected service if it is available.',
            ],
        ] as $key => $row) {
            $existing = DB::table('ui_texts')->where('key', $key)->first();

            if ($existing) {
                DB::table('ui_texts')->where('key', $key)->update([
                    'group' => $row['group'],
                    'label' => $row['label'],
                    'value_ru' => $row['ru'],
                    'value_en' => $row['en'],
                    'is_active' => true,
                    'updated_at' => $now,
                ]);

                continue;
            }

            $position += 10;
            DB::table('ui_texts')->insert([
                'key' => $key,
                'group' => $row['group'],
                'label' => $row['label'],
                'value_ru' => $row['ru'],
                'value_en' => $row['en'],
                'position' => $position,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};
