<?php

declare(strict_types=1);

use Yiisoft\Queue\Message\MessageHandlerInterface;
use Yiisoft\Queue\Message\MessageInterface;

/**
 * Обработчик очереди: отправка SMS подписчику.
 */
class SendSmsHandler implements MessageHandlerInterface
{
    public function handle(MessageInterface $message): void
    {
        $data = $message->getData();
        $phone = $data['phone'] ?? null;
        $text = $data['text'] ?? '';

        if (!$phone) {
            return;
        }

        /** @var Notifier $notifier */
        $notifier = Yii::app()->notifier;
        $notifier->sendSms($phone, $text);
    }
}
