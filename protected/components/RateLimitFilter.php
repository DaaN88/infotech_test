<?php

declare(strict_types=1);

/**
 * Simple rate limiting filter for controller actions.
 */
class RateLimitFilter extends CFilter
{
    /** @var int Maximum attempts allowed within the decay window. */
    public int $maxAttempts = 5;

    /** @var int Window size in seconds. */
    public int $decaySeconds = 60;

    /** @var string Cache key prefix to avoid collisions. */
    public string $cachePrefix = 'rate_limit';

    protected function preFilter($filterChain)
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->request;
        /** @var CCache|null $cache */
        $cache = Yii::app()->cache;

        if ($cache === null) {
            return true;
        }

        $key = $this->buildCacheKey($filterChain, $request);
        $attempts = (int) $cache->get($key);

        if ($attempts >= $this->maxAttempts) {
            $this->renderLimitExceeded($request);

            return false;
        }

        $cache->set($key, $attempts + 1, $this->decaySeconds);

        return true;
    }

    private function buildCacheKey(CFilterChain $filterChain, CHttpRequest $request): string
    {
        $ip = $request->getUserHostAddress() ?: 'unknown';

        return sprintf(
            '%s:%s:%s:%s',
            $this->cachePrefix,
            $filterChain->controller->id,
            $filterChain->action->id,
            $ip
        );
    }

    private function renderLimitExceeded(CHttpRequest $request): void
    {
        $message = Yii::t(
            'app',
            'auth.rate_limited',
            ['{seconds}' => $this->decaySeconds]
        );

        if (! headers_sent()) {
            http_response_code(429);
            header('Content-Type: text/plain; charset=utf-8');
        }

        if ($request instanceof TestHttpRequest) {
            $request->lastRedirectCode = 429;
        }

        echo $message;

        if (! defined('PHPUNIT_RUNNING') || ! PHPUNIT_RUNNING) {
            Yii::app()->end();
        }
    }
}
