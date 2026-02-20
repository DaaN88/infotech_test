<?php
declare(strict_types=1);

class LoginThrottleTest extends FunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();
        Yii::app()->cache->flush();
    }

    public function testLoginRateLimitBlocksAfterFiveAttempts(): void
    {
        $payload = [
            'LoginForm' => [
                'username' => 'admin',
                'password' => 'wrong',
            ],
        ];

        // First five attempts should render the form (no redirect, no throttle).
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('auth/login', $payload);
            $this->assertNull($response['redirectUrl']);
            $this->assertContains('login-form', $response['content']);
        }

        // Sixth attempt should be throttled.
        $response = $this->post('auth/login', $payload);

        $this->assertContains('Слишком много попыток входа', $response['content']);
        $this->assertEquals(429, $response['statusCode']);
    }
}
