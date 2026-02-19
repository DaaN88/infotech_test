<?php
declare(strict_types=1);

class LoginPageTest extends FunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Проверяет, что страница входа успешно открывается и содержит форму логина.
     */
    public function testLoginPageLoads()
    {
        $response = $this->get('auth/login');

        $this->assertContains('Вход', $response['content']);
        $this->assertContains('login-form', $response['content']);
        $this->assertContains('admin/admin', $response['content']);
        $this->assertNull($response['redirectUrl']);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}
