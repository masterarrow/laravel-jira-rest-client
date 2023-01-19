<?php

namespace Atlassian\JiraRest\Requests\Middleware;

use Closure;

class OAuth2Middleware
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @param array $options ['accessToken' => string]
     */
    public function __construct($options)
    {
        $this->accessToken = $options['accessToken'];
    }

    /**
     * Handle an incoming request.
     *
     * @param $options
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($options, Closure $next)
    {
        $options['headers']['Authorization'] = 'Bearer ' . $this->accessToken;

        return $next($options);
    }
}
