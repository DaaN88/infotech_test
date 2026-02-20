<?php
declare(strict_types=1);

class DummyNotificationService extends NotificationService
{
    public static int $enqueued = 0;

    public function enqueueNewBook(Book $book): void
    {
        self::$enqueued++;
        // Не вызываем очередь в тестах.
    }
}

class DummyQueueComponent extends CApplicationComponent
{
    /** @var Message[] */
    public array $pushed = array();

    public function push($message)
    {
        $this->pushed[] = $message;
        return $message;
    }
}

class BookServiceTest extends CDbTestCase
{
    protected $fixtures = array(
        'users' => 'User',
        'authors' => 'Author',
        'books' => 'Book',
        'book_author' => ':book_author',
        'book_photos' => ':book_photos',
        'subscriptions' => 'Subscription',
    );

    private BookService $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = Yii::app()->bookService;
        DummyNotificationService::$enqueued = 0;
        Yii::app()->setComponent('notificationService', new DummyNotificationService());
    }

    public function testCreateEnqueuesJobsForSubscribedAuthor()
    {
        // подписка на автора 1
        $sub = new Subscription();
        $sub->author_id = 1;
        $sub->phone = '+79991112233';
        $sub->name = 'Подписчик';
        $sub->created_at = date('Y-m-d H:i:s');
        $this->assertTrue($sub->save());

        // заглушка очереди
        $queue = new DummyQueueComponent();
        Yii::app()->setComponent('queue', $queue);
        // используем реальный NotificationService, чтобы он пушил в очередь
        Yii::app()->setComponent('notificationService', new NotificationService());

        $attrs = array(
            'title' => 'Новая книга с уведомлением',
            'year' => 2025,
            'isbn' => '978-9-12345-678-0',
            'description' => 'Тест уведомления',
        );

        $book = $this->service->create($attrs, array(1), null);

        $this->assertNotNull($book->id);
        $this->assertCount(1, $queue->pushed, 'Должна быть поставлена одна задача отправки СМС');
    }

    public function testCreateBookPersistsAndNotifies()
    {
        $attrs = array(
            'title' => 'Новая книга',
            'year' => 2025,
            'isbn' => '978-9-99999-999-9',
            'description' => 'Тестовое описание',
        );

        $book = $this->service->create($attrs, array(1, 2), null);

        $this->assertNotNull(Book::model()->findByPk($book->id));
        $this->assertCount(2, $book->authors);
        $this->assertEquals(1, DummyNotificationService::$enqueued);
    }

    public function testUpdateRemovesAuthorAndCleansSubscriptions()
    {
        // Автор 3 имеет только книгу 2. Создаём подписку на него.
        $sub = new Subscription();
        $sub->author_id = 3;
        $sub->phone = '+79990000001';
        $sub->name = 'Подписчик';
        $sub->created_at = date('Y-m-d H:i:s');
        $this->assertTrue($sub->save());

        $book = Book::model()->findByPk(2);
        $data = $book->attributes;
        $data['title'] = 'Архитектура систем v2';

        $updated = $this->service->update(2, $data, array(2), null); // меняем автора на 2 (Мария)

        $this->assertEquals('Архитектура систем v2', $updated->title);
        $authors = array_values(array_map('intval', CHtml::listData($updated->authors, 'id', 'id')));
        $this->assertEquals(array(2), $authors);

        // подписка на автора 3 должна исчезнуть, т.к. книг автора не осталось
        $this->assertCount(0, Subscription::model()->findAllByAttributes(array('author_id' => 3)));
    }

    public function testDeleteRemovesSubscriptions()
    {
        $sub = new Subscription();
        $sub->author_id = 3;
        $sub->phone = '+79990000002';
        $sub->name = 'Подписчик';
        $sub->created_at = date('Y-m-d H:i:s');
        $this->assertTrue($sub->save());

        $this->service->delete(2); // удаляем книгу автора 3

        $this->assertNull(Book::model()->findByPk(2));
        $this->assertCount(0, Subscription::model()->findAllByAttributes(array('author_id' => 3)));
    }

    public function testCreateFailsWithoutAuthors()
    {
        $this->setExpectedException(ValidationException::class);
        $this->service->create(
            array('title' => 'Без автора', 'year' => 2024, 'isbn' => '978-0-00000-000-0'),
            array(),
            null
        );
    }
}
