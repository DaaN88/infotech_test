<?php

declare(strict_types=1);

class SmsNotificationChannel implements NotificationChannelInterface
{
    private SmsClient $client;

    public function __construct(SmsClient $client)
    {
        $this->client = $client;
    }

    public function send(string $recipient, string $message): void
    {
        $this->client->send($recipient, $message);
    }
}
