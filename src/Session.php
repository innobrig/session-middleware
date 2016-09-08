<?php

namespace InnoBrig\SessionMiddleware;

use JoeBengalen\Config\AbstractConfig;


/**
 * Session object.
 *
 * The session object makes use of a namespace to not interfere
 * with other code using the $_SESSION global.
 */
class Session extends AbstractConfig implements SessionInterface
{
    /**
     * @var string Unique session namespace.
     */
    protected $namespace = 'innobrig.session';

    /**
     * @var array Reference to the $_SESSION data within the namespace.
     */
    protected $data = [];

    protected $options = [
        'name'          => 'InnoBrigSession',
        'lifetime'      => 3600,
        'path'          => null,
        'domain'        => null,
        'secure'        => false,
        'httponly'      => true,
        'cache_limiter' => 'nocache',
    ];

    /**
     * Create a new session namespace.
     *
     * @param string $namespace Session namespace.
     * @param bool $initialize Whether to start a PHP session, register and reference the namespace in the constructor.
     */
    public function __construct($options, $initialize = true)
    {
        $this->options = $options;

        if (isset($options['namespace']) && $options['namespace'] !== null) {
            $this->setNamespace($options['namespace']);
        }

        if ($initialize) {
            $this->start();
            $this->registerNamespace();
            $this->referenceNamespace();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->isActive()) {
            return $this;
        }

        $options = $this->options;

        //var_dump ($options); exit();
        $current = session_get_cookie_params();

        $lifetime = $options['cookie_lifetime'] ?: $current['lifetime'];
        $path     = $options['cookie_path']     ?: $current['path'];
        $domain   = $options['cookie_domain']   ?: $current['domain'];
        $secure   = (bool)$options['cookie_secure'];
        $httponly = (bool)$options['cookie_httponly'];
        $name     = $options['cookie_name'];

        if (is_string($lifetime)) {
            $lifetime = strtotime($lifetime) - time();
        }

        session_set_cookie_params ($lifetime, $path, $domain, $secure, $httponly);
        session_name ($name);
        session_cache_limiter ($options['cacheLimiter']);

        if (session_id() && $options['autorefresh'] && isset($_COOKIE[$name])) {
            setcookie($name, $_COOKIE[$name], time() + $lifetime, $path, $domain, $secure, $httponly);
        }
        
        session_start();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy()
    {
        if ($this->isActive()) {
            $this->clear();
            session_destroy();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * {@inheritdoc}
     */
    public function registerNamespace()
    {
        if (!isset($_SESSION[$this->namespace]) || !is_array($_SESSION[$this->namespace])) {
            $_SESSION[$this->namespace] = [];
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function referenceNamespace()
    {
        // If some data was already set before referencing, merge the data
        if (!empty($this->data)) {
            // array_replace_recursive recusively merges both arrays. Where array_merge_recursive
            // makes an array makes an numeric array if different values are given to a string key,
            // does array_replace_recursive replace the value of an string key.
            $_SESSION[$this->namespace] = array_replace_recursive($_SESSION[$this->namespace], $this->data);
        }

        $this->data = &$_SESSION[$this->namespace];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}