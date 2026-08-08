<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Validation\ValidationException;
use Throwable;

final class AdminErrorMessage
{
    public static function fromThrowable(Throwable $exception): string
    {
        if ($exception instanceof ValidationException) {
            $messages = collect($exception->errors())
                ->flatten()
                ->filter()
                ->values();

            return $messages->isNotEmpty()
                ? 'Не удалось сохранить: ' . $messages->implode(' ')
                : 'Не удалось сохранить: проверьте обязательные поля формы.';
        }

        if ($exception instanceof PostTooLargeException) {
            return 'Не удалось сохранить: файл слишком большой для загрузки. Сожмите файл или загрузите изображение через галерею.';
        }

        if ($exception instanceof QueryException) {
            return self::fromQueryException($exception);
        }

        $message = trim($exception->getMessage());

        return $message !== ''
            ? 'Не удалось сохранить: ' . $message
            : 'Не удалось сохранить: сервер вернул неизвестную ошибку. Попробуйте обновить страницу и повторить сохранение.';
    }

    private static function fromQueryException(QueryException $exception): string
    {
        $message = $exception->getMessage();

        if (preg_match("/Unknown column '([^']+)'/i", $message, $matches) === 1) {
            return sprintf(
                'Не удалось сохранить: в базе пока нет поля "%s". Нужно применить миграции и повторить сохранение.',
                $matches[1],
            );
        }

        if (str_contains($message, 'Duplicate entry')) {
            return 'Не удалось сохранить: такое значение уже есть в базе. Чаще всего занят slug или другой уникальный адрес.';
        }

        if (str_contains($message, 'Data too long')) {
            return 'Не удалось сохранить: одно из полей слишком длинное для базы. Сократите текст или перенесите длинный список в многострочное поле.';
        }

        if (str_contains($message, 'cannot be null') || (str_contains($message, 'Column') && str_contains($message, 'null'))) {
            return 'Не удалось сохранить: не заполнено обязательное поле базы. Проверьте красные обязательные поля в форме.';
        }

        $detail = $exception->getPrevious()?->getMessage() ?: $message;

        return 'Не удалось сохранить: ошибка базы данных. Подробность: ' . $detail;
    }
}
