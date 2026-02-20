<?php

declare(strict_types=1);

class SmsClient
{
    private string $apiKey;
    private string $endpoint;

    public function __construct(string $apiKey, string $endpoint = 'https://smspilot.ru/api.php')
    {
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
    }

    /**
     * Отправляет SMS через smspilot.ru API (эмулятор).
     */
    public function send(string $phone, string $text): void
    {
        $query = http_build_query([
            'send' => $text,
            'to' => $phone,
            'apikey' => $this->apiKey,
            'format' => 'json',
        ]);

        $url = $this->endpoint . '?' . $query;

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5,
            ],
        ]);

        $previousHandler = set_error_handler(function ($severity, $message, $file = '', $line = 0) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            $result = file_get_contents($url, false, $ctx);

            if ($result === false) {
                throw new RuntimeException('Не удалось отправить SMS');
            }

            // Логируем ответ смспилота (эмулятор) для контроля доставки.
            $short = mb_substr((string) $result, 0, 500, 'UTF-8');
            Yii::log(
                sprintf(
                    'SMS sent via smspilot: phone=%s, text="%s", response=%s',
                    $phone,
                    $this->truncate($text),
                    $short
                ),
                CLogger::LEVEL_INFO,
                'sms'
            );
        } catch (Throwable $e) {
            Yii::log(
                sprintf('Ошибка отправки SMS: %s (phone=%s)', $e->getMessage(), $phone),
                CLogger::LEVEL_ERROR,
                'sms'
            );

            throw new RuntimeException('Не удалось отправить SMS', 0, $e);
        } finally {
            if ($previousHandler !== null) {
                set_error_handler($previousHandler);
            } else {
                restore_error_handler();
            }
        }
    }

    /**
     * Усечём длинный текст для логов.
     */
    private function truncate(string $text, int $limit = 120): string
    {
        return mb_strlen($text, 'UTF-8') > $limit
            ? mb_substr($text, 0, $limit, 'UTF-8') . '...'
            : $text;
    }
}
