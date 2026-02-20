<?php
declare(strict_types=1);

class SmsClientTest extends CTestCase
{
    /**
     * Проверяет, что сетевые ошибки оборачиваются в RuntimeException (а не пробрасываются как предупреждения).
     */
    public function testSendThrowsRuntimeExceptionOnNetworkError()
    {
        // Невалидный endpoint, чтобы гарантированно получить ошибку соединения
        $client = new SmsClient('dummy-key', 'http://127.0.0.1:1');

        $this->setExpectedException('RuntimeException');
        $client->send('+70000000000', 'test');
    }
}
