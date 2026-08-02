<?php

use App\Models\UiText;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            'quiz.intro' => [
                'label' => 'Квиз: вводный текст',
                'ru' => 'Ответьте на 5 вопросов, и мы подготовим персональное предложение. Бонусом откроем PDF по выбранной услуге, если он загружен в CMS.',
                'en' => 'Answer 5 questions and we will prepare a personal proposal. As a bonus, we will open the PDF for the selected service if it is uploaded in the CMS.',
            ],
            'quiz.finalText' => [
                'label' => 'Квиз: финальный текст',
                'ru' => 'Оставьте контакт, а мы подготовим персональное предложение. Если для услуги загружен PDF-бонус, ссылка появится сразу после отправки заявки.',
                'en' => 'Leave your contact details and we will prepare a personal proposal. If the service has a PDF bonus, the link will appear right after submitting the request.',
            ],
            'quiz.pdfDownloadButton' => [
                'label' => 'Квиз: скачать PDF',
                'ru' => 'Скачать PDF-бонус',
                'en' => 'Download PDF bonus',
            ],
        ];

        foreach ($rows as $key => $row) {
            $text = UiText::query()->firstOrNew(['key' => $key]);
            $text->group = 'quiz';
            $text->label = $row['label'];
            $text->value_ru = $row['ru'];
            $text->value_en = $row['en'];
            $text->position = $text->position ?: ((int) UiText::query()->max('position') + 10);
            $text->is_active = true;
            $text->save();
        }
    }

    public function down(): void
    {
        UiText::query()->where('key', 'quiz.pdfDownloadButton')->delete();
    }
};
