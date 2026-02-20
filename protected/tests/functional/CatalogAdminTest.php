<?php
declare(strict_types=1);

class CatalogAdminTest extends FunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Проверяет, что валидный логин переводит на каталог и отображает админские действия.
     */
    public function testLoginWithValidCredentialsRedirectsToCatalog()
    {
        $response = $this->post('auth/login', array(
            'LoginForm' => array(
                'username' => 'admin',
                'password' => 'admin',
                'rememberMe' => 0,
            ),
        ));

        $this->assertFalse(Yii::app()->user->isGuest);
        $this->assertEquals('admin', Yii::app()->user->name);
        $this->assertNotNull($response['redirectUrl']);
        $this->assertContains('book/index', $response['redirectUrl']);

        $catalog = $this->get('book/index');
        $this->assertContains('Редактировать', $catalog['content']);
        $this->assertContains('Удалить', $catalog['content']);
        $this->assertNotContains('Подписаться', $catalog['content']);
        $this->assertContains('Главная', $catalog['content'], 'Должна отображаться локализованная ссылка в хлебных крошках');
        $this->assertContains('Страница:', $catalog['content'], 'Должен выводиться локализованный заголовок пагинации');
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}
