<?php
declare(strict_types=1);

class DummyNotifierQueue extends Notifier
{
    public static array $sent = array();

    public function sendSms(string $phone, string $message): void
    {
        self::$sent[] = compact('phone', 'message');
    }
}
