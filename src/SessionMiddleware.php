<?php
/**
 * Start a session.
 *
 * Copyright 2015 Rob Allen (rob@akrabat.com).
 * License: New-BSD
 */
namespace InnoBrig\SessionMiddleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


final class SessionMiddleware
{
    protected $options = [
        'autorefresh'           => true,                            // Whether to extend session lifetime after each user activity
        'bindToIpAddress'       => true,                            // Log user out if IP address changes
        'bindToUserAgent'       => true,                            // Log user out if user agent changes
        'cacheLimiter'          => 'nocache',                       // Cache Limiter to be set on session
        'cookie_domain'         => null,                            // Session cookie domain
        'cookie_httponly'       => true,                            // Session cookie httponly setting
        'cookie_lifetime'       => '1 hour',                        // Session cookie lifetime, can be # of seconds or any string parseable by strtotime()
        'cookie_name'           => 'BoostCMS_Session',              // Session cookie name
        'cookie_path'           => null,                            // Session cookie path
        'cookie_secure'         => false,                           // Session cookie secure: whether or not to use HTTPS
        'namespace'             => 'InnoBrig'
    ];


    public function __construct($options = [])
    {
        foreach ($options as $k=>$v) {
            $this->options[$k] = $v;
        }
    }


    /**
     * Invoke middleware
     *
     * @param  RequestInterface  $request  PSR7 request object
     * @param  ResponseInterface $response PSR7 response object
     * @param  callable          $next     Next middleware callable
     *
     * @return ResponseInterface PSR7 response object
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        global $app;
        $container = $app->getContainer ();
        $session   = new Session ($this->options);

        $container['session'] = $session;

        return $next($request, $response);
    }
}