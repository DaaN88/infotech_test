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

class BookNotificationQueueTest extends FunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();
        DummyNotifierQueue::$sent = array();
        Yii::app()->setComponent('notifier', new DummyNotifierQueue());
        Yii::app()->setComponent('notificationService', new NotificationService());

        // гарантируем sync-драйвер в тестах
        Yii::app()->queue->driver = 'sync';
    }

    /**
     * При создании книги для автора с подпиской задача уведомления должна отправить SMS (sync-драйвер).
     */
    public function testCreateBookEnqueuesAndSendsSms()
    {
        // подписка на автора 1
        $sub = new Subscription();
        $sub->author_id = 1;
        $sub->phone = '+79990000009';
        $sub->name = 'QueueTester';
        $sub->created_at = date('Y-m-d H:i:s');
        $this->assertTrue($sub->save());

        // создаём книгу с автором 1
        $svc = Yii::app()->bookService;
        $attrs = array(
            'title' => 'Queue Test Book',
            'year' => 2026,
            'isbn' => '978-9-99999-777-7',
            'description' => 'queue functional test',
        );

        $book = $svc->create($attrs, array(1), null);

        // sync-драйвер требует явного запуска run() для обработки сообщений в тестах
        Yii::app()->queue->run();

        $this->assertNotNull($book->id);
        $this->assertCount(1, DummyNotifierQueue::$sent);
        $msg = DummyNotifierQueue::$sent[0];
        $this->assertEquals('+79990000009', $msg['phone']);
        $this->assertContains($book->title, $msg['message']);
    }
}
