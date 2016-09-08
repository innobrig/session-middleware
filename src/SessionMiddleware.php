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
        'name'          => 'BoostCMS_Session',
        'lifetime'      => 3600,
        'path'          => null,
        'domain'        => null,
        'secure'        => false,
        'httponly'      => true,
        'cache_limiter' => 'nocache',
    ];


    public function __construct($options = [])
    {
        $this->options = $options;
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
        $session   = new \InnoBrig\SessionMiddleware\Session ($this->options);

        $container['session'] = $session;

        return $next($request, $response);
    }


    public function start()
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            return;
        }

        $options = $this->options;
        $current = session_get_cookie_params();

        $lifetime = (int)($options['lifetime'] ?: $current['lifetime']);
        $path     = $options['path'] ?: $current['path'];
        $domain   = $options['domain'] ?: $current['domain'];
        $secure   = (bool)$options['secure'];
        $httponly = (bool)$options['httponly'];

        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        
        /*
        if (session_id()) {
            if ($settings['autorefresh'] && isset($_COOKIE[$name])) {
                setcookie(
                    $name,
                    $_COOKIE[$name],
                    time() + $settings['lifetime'],
                    $settings['path'],
                    $settings['domain'],
                    $settings['secure'],
                    $settings['httponly']
                );
            }
        }
        */

        session_name($options['name']);
        session_cache_limiter($options['cache_limiter']);
        session_start();
    }
}