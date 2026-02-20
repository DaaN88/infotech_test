<?php
declare(strict_types=1);

class DummyNotifierQueue extends Notifier
{
    public static  $sent = [];

    public function sendSms(string $phone, string $message): void
    {
        self::$sent[] = compact('phone', 'message');
    }
}
