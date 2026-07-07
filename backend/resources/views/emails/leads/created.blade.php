<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Новая заявка</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.5;">
    <h1 style="font-size: 22px; margin: 0 0 16px;">Новая заявка #{{ $lead->id }}</h1>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; min-width: 320px;">
        <tr>
            <td style="font-weight: 700; border-bottom: 1px solid #e5e7eb;">Имя</td>
            <td style="border-bottom: 1px solid #e5e7eb;">{{ $lead->name ?: 'Не указано' }}</td>
        </tr>
        <tr>
            <td style="font-weight: 700; border-bottom: 1px solid #e5e7eb;">Контакт</td>
            <td style="border-bottom: 1px solid #e5e7eb;">{{ $lead->contact }}</td>
        </tr>
        <tr>
            <td style="font-weight: 700; border-bottom: 1px solid #e5e7eb;">Услуга</td>
            <td style="border-bottom: 1px solid #e5e7eb;">{{ $lead->service ?: 'Не указано' }}</td>
        </tr>
        <tr>
            <td style="font-weight: 700; border-bottom: 1px solid #e5e7eb;">Источник</td>
            <td style="border-bottom: 1px solid #e5e7eb;">{{ $lead->source ?: 'site' }}</td>
        </tr>
        <tr>
            <td style="font-weight: 700; border-bottom: 1px solid #e5e7eb;">Канал</td>
            <td style="border-bottom: 1px solid #e5e7eb;">{{ $lead->channel ?: 'Не указан' }}</td>
        </tr>
        <tr>
            <td style="font-weight: 700; border-bottom: 1px solid #e5e7eb;">Дата</td>
            <td style="border-bottom: 1px solid #e5e7eb;">{{ $lead->created_at?->format('d.m.Y H:i') }}</td>
        </tr>
    </table>

    <h2 style="font-size: 18px; margin: 24px 0 8px;">Сообщение</h2>
    <div style="padding: 12px; background: #f8fafc; border: 1px solid #e5e7eb;">
        {!! nl2br(e($lead->message ?: 'Сообщение не указано')) !!}
    </div>

    @if (! empty($lead->payload))
        <h2 style="font-size: 18px; margin: 24px 0 8px;">Дополнительные данные</h2>
        <pre style="padding: 12px; background: #f8fafc; border: 1px solid #e5e7eb; white-space: pre-wrap;">{{ json_encode($lead->payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
    @endif
</body>
</html>
