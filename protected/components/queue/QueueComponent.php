<?php

declare(strict_types=1);

use Psr\Log\NullLogger;
use Yiisoft\Injector\Injector;
use Yiisoft\Queue\Adapter\SynchronousAdapter;
use Yiisoft\Queue\Cli\SimpleLoop;
use Yiisoft\Queue\Message\JsonMessageSerializer;
use Yiisoft\Queue\Message\MessageInterface;
use Yiisoft\Queue\Middleware\CallableFactory;
use Yiisoft\Queue\Middleware\Consume\ConsumeMiddlewareDispatcher;
use Yiisoft\Queue\Middleware\Consume\MiddlewareFactoryConsume;
use Yiisoft\Queue\Middleware\FailureHandling\FailureMiddlewareDispatcher;
use Yiisoft\Queue\Middleware\FailureHandling\MiddlewareFactoryFailure;
use Yiisoft\Queue\Middleware\Push\MiddlewareFactoryPush;
use Yiisoft\Queue\Middleware\Push\PushMiddlewareDispatcher;
use Yiisoft\Queue\Queue;
use Yiisoft\Queue\QueueInterface;
use Yiisoft\Queue\Redis\Adapter as RedisAdapter;
use Yiisoft\Queue\Redis\QueueProvider as RedisQueueProvider;
use Yiisoft\Queue\Worker\Worker;

class QueueComponent extends CApplicationComponent
{
    /**
     * Драйвер очереди: redis|sync
     */
    public string $driver = 'redis';

    public string $channel = 'book-notify';
    public string $redisHost = 'redis';
    public int $redisPort = 6379;
    public int $redisDb = 0;
    public ?string $redisPassword = null;

    private QueueInterface $queue;

    public function init(): void
    {
        parent::init();

        $container = new QueueContainer();
        $logger = new NullLogger();
        $loop = new SimpleLoop();
        $callableFactory = new CallableFactory($container);

        $consumeDispatcher = new ConsumeMiddlewareDispatcher(
            new MiddlewareFactoryConsume($container, $callableFactory)
        );
        $failureDispatcher = new FailureMiddlewareDispatcher(
            new MiddlewareFactoryFailure($container, $callableFactory),
            []
        );
        $pushDispatcher = new PushMiddlewareDispatcher(
            new MiddlewareFactoryPush($container, $callableFactory)
        );

        $handlers = [
            SendSmsHandler::class => [SendSmsHandler::class, 'handle'],
        ];

        $worker = new Worker(
            $handlers,
            $logger,
            new Injector($container),
            $container,
            $consumeDispatcher,
            $failureDispatcher
        );

        $adapter = $this->createAdapter($worker, $loop);

        $this->queue = new Queue(
            $worker,
            $loop,
            $logger,
            $pushDispatcher,
            $adapter
        );
    }

    private function createAdapter(Worker $worker, SimpleLoop $loop): SynchronousAdapter|RedisAdapter
    {
        if ($this->driver === 'sync') {
            return new SynchronousAdapter($worker, new SyncInMemoryQueue(), 'sync');
        }

        $redis = new Redis();

        try {
            $redis->connect($this->redisHost, $this->redisPort);
        } catch (\RedisException $e) {
            // fallback для локального запуска без docker-сети
            if ($this->redisHost !== '127.0.0.1' && $this->redisHost !== 'localhost') {
                $redis->connect('127.0.0.1', $this->redisPort);
            } else {
                throw $e;
            }
        }
        if ($this->redisPassword) {
            $redis->auth($this->redisPassword);
        }
        if ($this->redisDb > 0) {
            $redis->select($this->redisDb);
        }

        $provider = new RedisQueueProvider($redis, $this->channel);
        $serializer = new JsonMessageSerializer();

        return new RedisAdapter($provider, $serializer, $loop);
    }

    public function push(MessageInterface $message): MessageInterface
    {
        return $this->queue->push($message);
    }

    public function run(int $max = 0): int
    {
        return $this->queue->run($max);
    }

    public function listen(): void
    {
        $this->queue->listen();
    }

    public function getQueue(): QueueInterface
    {
        return $this->queue;
    }
}
