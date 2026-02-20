<?php

declare(strict_types=1);

class Notifier extends CApplicationComponent
{
    /**
     * @var NotificationFactoryInterface
     */
    public NotificationFactoryInterface $factory;

    public function init()
    {
        parent::init();

        if (!isset($this->factory)) {
            $this->factory = Yii::app()->notificationFactory;
        }
    }

    public function sendSms(string $phone, string $message): void
    {
        $channel = $this->factory->createSmsChannel();
        $channel->send($phone, $message);
    }
}
