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

        $result = @file_get_contents($url, false, $ctx);

        if ($result === false) {
            throw new RuntimeException('Не удалось отправить SMS');
        }
    }
}
