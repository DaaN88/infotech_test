<?php

declare(strict_types=1);

abstract class FunctionalTestCase extends CDbTestCase
{
    protected $fixtures = array(
        'users' => 'User',
        'authors' => 'Author',
        'books' => 'Book',
        'book_author' => ':book_author',
        'book_photos' => ':book_photos',
        'subscriptions' => 'Subscription',
    );

    protected function setUp()
    {
        parent::setUp();
        if (!isset($_SESSION)) {
            $_SESSION = array();
        }
        Yii::app()->session->open();
        Yii::app()->user->logout(false);
        // Подключаем общие заглушки/моки для функциональных тестов
        foreach (glob(__DIR__ . '/../support/*.php') as $supportFile) {
            require_once $supportFile;
        }
        $this->resetRequestState();
    }

    protected function tearDown()
    {
        Yii::app()->user->logout(false);
        parent::tearDown();
    }

    protected function getRequest(): TestHttpRequest
    {
        return Yii::app()->getRequest();
    }

    protected function get(string $route, array $query = array())
    {
        return $this->dispatch($route, 'GET', $query, array());
    }

    protected function post(string $route, array $data = array())
    {
        return $this->dispatch($route, 'POST', array(), $data);
    }

    protected function dispatch(string $route, string $method, array $query, array $post): array
    {
        $this->resetRequestState($method, $route, $query, $post);

        ob_start();

        try {
            Yii::app()->runController($route);
        } catch (TestRedirectException $e) {
            // Redirects are captured by the request stub; nothing else to do.
        }

        $content = ob_get_clean();

        $request = $this->getRequest();

        return array(
            'content' => $content,
            'redirectUrl' => $request->lastRedirectUrl,
            'statusCode' => $request->lastRedirectCode,
        );
    }

    protected function resetRequestState(
        string $method = 'GET',
        string $route = '/',
        array $query = array(),
        array $post = array()
    ): void {
        $request = $this->getRequest();
        $request->clearRedirect();

        $_GET = $query;
        $_POST = $post;
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI'] = '/' . ltrim($route, '/');
    }
}
