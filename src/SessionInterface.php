<?php

namespace InnoBrig\SessionMiddleware;


interface SessionInterface extends \ArrayAccess
{
    /**
     * Start a PHP session if none is active.
     *
     * @return self.
     */
    public function start();

    /**
     * Clear all sesson data withing the namespace and destroy
     * the PHP session if one is active.
     *
     * @return self.
     */
    public function destroy();

    /**
     * Check if a PHP session is active.
     *
     * @return bool True is a session is started, false if not.
     */
    public function isActive();

    /**
     * Register the namespace in the $_SESSION global is is does not exist.
     *
     * @return self.
     */
    public function registerNamespace();

    /**
     * Reference the $_SESSION global namespace to this data.
     *
     * @return self.
     */
    public function referenceNamespace();

    /**
     * Set the session namespace.
     *
     * @param string $namespace Session namespace.
     *
     * @return self.
     */
    public function setNamespace($namespace);

    /**
     * Get the session namespace.
     *
     * @return string Session namespace.
     */
    public function getNamespace();

    /**
     * Set one or more session values.
     *
     * @param string|array $key   Session key or an array of keys and values.
     * @param mixed|null   $value Session value or null if $key is given an array.
     *
     * @return self.
     */
    public function set($key, $value = null);

    /**
     * Check if a session value is set.
     *
     * @param string $key Session key to check. If null is given it will check if any value is set at all.
     *
     * @return bool True if the key exists, false if not.
     */
    public function has($key = null);

    /**
     * Get a session value.
     *
     * @param string $key     Session key whose value to get.
     * @param mixed  $default Default value if the searched key is not found.
     *
     * @return mixed Matching session value or $default if the key was not found.
     */
    public function get($key = null, $default = null);

    /**
     * Remove a session value.
     *
     * @param string $key Session key to remove.
     *
     * @return self.
     */
    public function remove($key);

    /**
     * Clear all session values.
     */
    public function clear();
}