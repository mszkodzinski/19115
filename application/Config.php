<?php
class Config
{
    /**
     * @var null|array
     */
    protected static $_config = null;


    /**
     *
     */
    public static function load()
    {
        self::$_config = require_once '../config/config.php';
    }

    /**
     * @param null $name
     * @return array|null
     */
    public static function get($name = null)
    {
        if (self::$_config === null) {
            self::load();
        }
        if ($name) {
            return isset(self::$_config[$name]) ? self::$_config[$name] : null;
        }
        return self::$_config;
    }
}