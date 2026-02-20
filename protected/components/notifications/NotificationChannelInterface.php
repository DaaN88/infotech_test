<?php

declare(strict_types=1);

interface NotificationChannelInterface
{
    public function send(string $recipient, string $message): void;
}
