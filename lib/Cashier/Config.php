<?php

namespace Cashier;

class Config
{

  private static $bag = array(
                    // defaults
                    'driver' => 'php',
                    'expires' => 300,
                    // cache
                    'cache_dir' => './tmp',
                  );



  public static function set($key, $value = NULL)
  {
    static::$bag[$key] = $value;
  }

  public static function get($key, $default = FALSE)
  {
    return isset(static::$bag[$key]) ? static::$bag[$key] : $default;
  }

}
