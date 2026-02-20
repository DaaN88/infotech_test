<?php

declare(strict_types=1);

class NotificationFactory extends CApplicationComponent implements NotificationFactoryInterface
{
    /**
     * Тестовый ключ для smspilot (можно переопределить в params/env).
     */
    public string $smsApiKey = '';

    /**
     * Базовый endpoint API smspilot.
     */
    public string $smsEndpoint = 'https://smspilot.ru/api.php';

    private SmsClient $smsClient;

    public function init()
    {
        parent::init();
        $this->smsClient = new SmsClient($this->smsApiKey, $this->smsEndpoint);
    }

    public function createSmsChannel(): NotificationChannelInterface
    {
        return new SmsNotificationChannel($this->smsClient);
    }
}
