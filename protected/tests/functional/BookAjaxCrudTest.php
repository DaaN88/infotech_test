<?php

declare(strict_types=1);

class BookAjaxCrudTest extends FunctionalTestCase
{
    /**
     * Проверяем, что AJAX-удаление существующей книги отдаёт JSON и не падает в 404 из-за неверного маршрута.
     */
    public function testDeleteReturnsJsonSuccess()
    {
        // эмулируем авторизованного пользователя
        Yii::app()->user->setId(1);
        Yii::app()->user->setState('role', 'admin');

        // отметим запрос как AJAX
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $response = $this->dispatch('book/delete', 'POST', array('id' => 1), array());

        $data = json_decode($response['content'], true);
        $this->assertNotNull($data, 'Ответ должен быть JSON');
        $this->assertArrayHasKey('success', $data);
        $this->assertTrue($data['success']);
        $this->assertEquals(1, $data['id']);
    }
}
