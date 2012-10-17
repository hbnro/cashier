<?php

namespace Cashier;

class Base
{

  private static $obj = NULL;
  private static $last = array();

  private static $available = array(
                    'apc' => '\\Cashier\\Driver\\APC',
                    'php' => '\\Cashier\\Driver\\PHP',
                    'sqlite' => '\\Cashier\\Driver\\SQLite',
                    'memcache' => '\\Cashier\\Driver\\Memcache',
                    'filesystem' => '\\Cashier\\Driver\\Filesystem',
                  );



  public static function begin($key = NULL)
  {
    $key = $key ?: '__LEVEL' . ob_get_level();

    if (static::exists($key)) {
      echo static::fetch($key);
      return FALSE;
    }

    static::$last []= $key;

    ob_start();

    return TRUE;
  }

  public static function end($max = 0, $tags = array())
  {
    if ( ! ob_get_level()) {
      return FALSE;
    }

    $out = ob_get_clean();
    echo $out;

    if ( ! ($key = array_pop(static::$last))) {
      return FALSE;
    }

    if ($max > 0) {
      return static::store($key, $out, $max, $tags);
    }
    return TRUE;
  }

  public static function block($key, $alive, \Closure $lambda)
  {
    if (($old = static::fetch($key)) === FALSE) {
      ob_start() && $lambda();

      $old = ob_get_clean();

      static::store($key, $old, $alive);
    }

    echo $old;
  }

  public static function fetch($key, $default = FALSE)
  {
    if (($old = static::instance()->fetch_item($key)) === FALSE) {
      return $default;
    }
    return $old;
  }

  public static function store($key, $value, $max = 0, $tags = array())
  {
    $max = $max > 0 ? $max : \Cashier\Config::get('expires');

    if (is_string($tags)) {
      $tags = array_filter(explode(',', $tags));
    }

    if ( ! empty($tags)) {
      $old = static::instance()->fetch_item('__CACHE_TAGS');
      $old = ! is_array($old) ? array() : $old;

      $old[$key] = $tags;

      static::instance()->store_item('__CACHE_TAGS', $old, 1234567890);
    }

    if ($max > 0) {
      return static::instance()->store_item($key, $value, $max);
    }

    static::remove($key);

    return FALSE;
  }

  public static function remove($key)
  {
    if (is_string($key) && (strpos($key, ',') !== FALSE)) {
      $key = array_filter(explode(',', $key));
    }


    if (is_array($key)) {
      $old = static::instance()->fetch_item('__CACHE_TAGS');

      foreach ((array) $old as $i => $val) {
        $diff = array_intersect($key, (array) $val);

        if (empty($diff)) {
          continue;
        }

        static::instance()->delete_item($i);

        unset($old[$i]);
      }
      return static::instance()->store_item('__CACHE_TAGS', $old, 1234567890);
    }
    return static::instance()->delete_item($key);
  }

  public static function clear()
  {
    static::instance()->free_all();
  }

  public static function exists($key)
  {
    return static::instance()->check_item($key);
  }


  private static function instance()
  {
    if ( ! static::$obj) {
      $aux = \Cashier\Config::get('driver');

      if ( ! isset(static::$available[$aux])) {
        throw new \Exception("The cache driver '$aux' does not exists.");
      }

      $klass = static::$available[$aux];
      static::$obj = new $klass;
    }

    return static::$obj;
  }

}
