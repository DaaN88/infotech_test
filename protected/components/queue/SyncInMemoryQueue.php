<?php

declare(strict_types=1);

use Yiisoft\Queue\Adapter\AdapterInterface;
use Yiisoft\Queue\JobStatus;
use Yiisoft\Queue\Message\MessageInterface;
use Yiisoft\Queue\Middleware\Push\MiddlewarePushInterface;
use Yiisoft\Queue\QueueInterface;

/**
 * Простейшая sync-реализация очереди для тестового режима.
 */
class SyncInMemoryQueue implements QueueInterface
{
    public function getChannel(): string
    {
        return 'sync';
    }

    public function push(
        MessageInterface $message,
        MiddlewarePushInterface|callable|array|string ...$middlewareDefinitions
    ): MessageInterface {
        return $message;
    }

    public function run(int $max = 0): int
    {
        return 0;
    }

    public function listen(): void
    {
    }

    public function status(string|int $id): JobStatus
    {
        return JobStatus::DONE;
    }

    public function withAdapter(AdapterInterface $adapter): static
    {
        return $this;
    }

    public function withMiddlewares(
        MiddlewarePushInterface|callable|array|string ...$middlewareDefinitions
    ): QueueInterface {
        return $this;
    }

    public function withMiddlewaresAdded(
        MiddlewarePushInterface|callable|array|string ...$middlewareDefinitions
    ): QueueInterface {
        return $this;
    }
}
