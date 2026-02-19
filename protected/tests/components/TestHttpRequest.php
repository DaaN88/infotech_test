<?php

declare(strict_types=1);

class TestRedirectException extends Exception
{
    public $url;
    public $statusCode;

    public function __construct(string $url, int $statusCode)
    {
        parent::__construct("Redirect to {$url} with status {$statusCode}", $statusCode);
        $this->url = $url;
        $this->statusCode = $statusCode;
    }
}

class TestHttpRequest extends CHttpRequest
{
    public $lastRedirectUrl;
    public $lastRedirectCode;

    public function redirect($url, $terminate = true, $statusCode = 302)
    {
        if (strpos($url, '/') === 0 && strpos($url, '//') !== 0) {
            $url = $this->getHostInfo() . $url;
        }

        $this->lastRedirectUrl = $url;
        $this->lastRedirectCode = (int) $statusCode;

        throw new TestRedirectException($url, (int) $statusCode);
    }

    public function clearRedirect(): void
    {
        $this->lastRedirectUrl = null;
        $this->lastRedirectCode = null;
    }
}
