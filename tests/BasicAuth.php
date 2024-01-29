<?php

namespace Sergmoro1\Imageable\Tests;

class BasicAuth
{
    /**
     * @var string $basic_auth_key
     */
    private static $basic_auth_key;
    
    /**
     * Set basic auth key
     *
     * @return void
     */
    public static function setKey(string $email, string $password): void
    {
        // Create Basic Api key
        self::$basic_auth_key = "Basic " . base64_encode($email . ':' . $password);
    }
    
    /**
     * Get basic auth key
     *
     * @return string
     */
    public static function getKey(): string
    {
        return self::$basic_auth_key;
    }
}
