<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Трейт двуязычных моделей.
 *
 * Повторяет эталонный паттерн, уже используемый в Vacancy / Faq / MenuItem:
 * - при сохранении базовое поле синхронизируется с *_ru (основной язык),
 *   чтобы старый код, читающий «голые» поля, продолжал работать;
 * - fieldRu()/fieldEn() возвращают локализованное значение с fallback на базовое.
 *
 * Список полей задаётся в свойстве $translatable модели.
 */
trait HasTranslations
{
    /**
     * Поля, имеющие *_ru и *_en варианты.
     */
    public function getTranslatable(): array
    {
        return property_exists($this, 'translatable') && is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    protected static function bootHasTranslations(): void
    {
        static::saving(static function (self $model): void {
            foreach ($model->getTranslatable() as $field) {
                $ruField = $field . '_ru';

                if (filled($model->{$ruField}) && ($model->isDirty($ruField) || blank($model->{$field}))) {
                    $model->{$field} = $model->{$ruField};

                    continue;
                }

                if (blank($model->{$ruField}) && filled($model->{$field})) {
                    $model->{$ruField} = $model->{$field};
                }
            }
        });
    }

    public function fieldRu(string $field): ?string
    {
        $ruField = $field . '_ru';

        return filled($this->{$ruField})
            ? (string) $this->{$ruField}
            : (filled($this->{$field}) ? (string) $this->{$field} : null);
    }

    public function fieldEn(string $field): ?string
    {
        $enField = $field . '_en';

        return filled($this->{$enField}) ? (string) $this->{$enField} : null;
    }
}
