<?php
declare(strict_types=1);

class SubscriptionTest extends FunctionalTestCase
{
    public function testGuestCanSubscribe()
    {
        $response = $this->post('subscription/create', array(
            'author' => 1,
            'Subscription' => array(
                'phone' => '+79991112233',
                'name' => 'Тестовый Гость',
            ),
        ));

        $this->assertContains('book/index', $response['redirectUrl']);

        $saved = Subscription::model()->findByAttributes(array(
            'author_id' => 1,
            'phone' => '+79991112233',
        ));
        $this->assertNotNull($saved);
        $this->assertEquals('Тестовый Гость', $saved->name);
    }

    public function testDuplicateSubscriptionIsRejected()
    {
        // первая подписка успешна
        $this->post('subscription/create', array(
            'author' => 2,
            'Subscription' => array(
                'phone' => '+71234567890',
                'name' => 'User',
            ),
        ));

        // вторая с тем же телефоном и автором
        $response = $this->post('subscription/create', array(
            'author' => 2,
            'Subscription' => array(
                'phone' => '+71234567890',
                'name' => 'User',
            ),
        ));

        $this->assertContains('Вы уже подписаны на этого автора.', $response['content']);
    }

    public function testGuestCanChooseAuthorWhenBookHasMultipleAuthors()
    {
        // книга 1 имеет двух авторов (1 и 2)
        $page = $this->get('subscription/create', array('book' => 1));
        $this->assertContains('Subscription_author_id', $page['content'], 'На форме должен быть выбор автора.');
        $this->assertContains('value="2"', $page['content']);

        $response = $this->post('subscription/create', array(
            'book' => 1,
            'Subscription' => array(
                'author_id' => 2,
                'phone' => '+70001112233',
                'name' => 'Гость',
            ),
        ));

        $this->assertContains('book/index', $response['redirectUrl']);

        $saved = Subscription::model()->findByAttributes(array(
            'author_id' => 2,
            'phone' => '+70001112233',
        ));
        $this->assertNotNull($saved);
        $this->assertEquals('Гость', $saved->name);
    }

    public function testGuestCanSubscribeWithoutBookParamShowsAllAuthors()
    {
        $page = $this->get('subscription/create');
        $this->assertContains('Subscription_author_id', $page['content']);
        $this->assertContains('Ксения Романова', $page['content']); // автор из фикстур
        $this->assertNotContains('Author Id', $page['content'], 'Метка поля должна быть на русском');
        $this->assertEquals(20, substr_count($page['content'], '<option'), 'Должны выводиться все авторы из БД');

        $response = $this->post('subscription/create', array(
            'Subscription' => array(
                'author_id' => 15, // Ксения Романова
                'phone' => '+79993332211',
                'name' => 'Гость без книги',
            ),
        ));

        $this->assertContains('book/index', $response['redirectUrl']);

        $saved = Subscription::model()->findByAttributes(array(
            'author_id' => 15,
            'phone' => '+79993332211',
        ));
        $this->assertNotNull($saved);
        $this->assertEquals('Гость без книги', $saved->name);
    }
}
