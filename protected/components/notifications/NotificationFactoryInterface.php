<?php

declare(strict_types=1);

interface NotificationFactoryInterface
{
    public function createSmsChannel(): NotificationChannelInterface;
}
