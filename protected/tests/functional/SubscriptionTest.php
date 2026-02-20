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
}
