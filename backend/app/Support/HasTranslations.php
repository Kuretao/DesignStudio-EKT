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
                $attributes = $model->getAttributes();
                $ruValue = $attributes[$ruField] ?? null;
                $baseValue = $attributes[$field] ?? null;

                if (filled($ruValue) && ($model->isDirty($ruField) || blank($baseValue))) {
                    $model->{$field} = $ruValue;

                    continue;
                }

                if ($model->isDirty($field) && ! $model->isDirty($ruField) && filled($baseValue)) {
                    $model->{$ruField} = $baseValue;

                    continue;
                }

                if (blank($ruValue) && filled($baseValue)) {
                    $model->{$ruField} = $baseValue;
                }
            }
        });
    }

    public function getAttribute($key): mixed
    {
        if (is_string($key) && str_ends_with($key, '_ru')) {
            $baseField = substr($key, 0, -3);

            if (in_array($baseField, $this->getTranslatable(), true)) {
                $value = parent::getAttribute($key);

                return filled($value) ? $value : parent::getAttribute($baseField);
            }
        }

        return parent::getAttribute($key);
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
