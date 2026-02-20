<?php
declare(strict_types=1);

class DummyNotifier extends Notifier
{
    public static array $sent = [];

    public function sendSms(string $phone, string $message): void
    {
        self::$sent[] = compact('phone', 'message');
    }
}

class NotificationServiceTest extends CDbTestCase
{
    protected $fixtures = array(
        'users' => 'User',
        'authors' => 'Author',
        'books' => 'Book',
        'book_author' => ':book_author',
        'subscriptions' => 'Subscription',
    );

    protected function setUp()
    {
        parent::setUp();
        DummyNotifier::$sent = [];
        Yii::app()->setComponent('notifier', new DummyNotifier());

        $queue = new QueueComponent();
        $queue->driver = 'sync';
        $queue->init();
        Yii::app()->setComponent('queue', $queue);
    }

    public function testEnqueueNewBookCreatesSmsJobs()
    {
        // создаём две подписки на автора 1
        foreach (['+70000000001', '+70000000002'] as $phone) {
            $sub = new Subscription();
            $sub->author_id = 1;
            $sub->phone = $phone;
            $sub->name = 'Test';
            $sub->created_at = date('Y-m-d H:i:s');
            $this->assertTrue($sub->save());
        }

        $book = Book::model()->findByPk(1);
        Yii::app()->notificationService->enqueueNewBook($book);

        // sync driver: выполняем очередь сразу
        Yii::app()->queue->run();

        $this->assertCount(2, DummyNotifier::$sent);
        $this->assertContains($book->title, DummyNotifier::$sent[0]['message']);
        $this->assertEquals('+70000000001', DummyNotifier::$sent[0]['phone']);
    }
}
