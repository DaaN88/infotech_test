<?php
declare(strict_types=1);

class CatalogGuestTest extends FunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Проверяет, что каталог доступен гостю и выводит демо-книги с авторами.
     */
    public function testCatalogPageLoadsForGuest()
    {
        $response = $this->get('book/index');

        $this->assertContains('Каталог книг', $response['content']);
        $this->assertContains('Путь разработчика', $response['content']);
        $this->assertContains('Архитектура систем', $response['content']);
        $this->assertContains('Иван Иванов, Мария Петрова', $response['content']);
        $this->assertContains('Джон Смит', $response['content']);
        $this->assertContains('Подписаться', $response['content']);
    }

    /**
     * Проверяет, что в списке присутствуют ISBN и описания из фикстур.
     */
    public function testCatalogContainsSeedData()
    {
        $response = $this->get('book/index');

        $this->assertContains('978-1-23456-789-0', $response['content']);
        $this->assertContains('978-1-11111-111-1', $response['content']);
        $this->assertContains('Практические советы по построению карьеры.', $response['content']);
        $this->assertContains('Шаблоны и подходы к проектированию сложных сервисов.', $response['content']);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}
