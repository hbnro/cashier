<?php

namespace Cashier\Driver;

class PHP
{

  public function free_all()
  {
    foreach (glob(\Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_PHP*') as $cache_file) {
      @unlink($cache_file);
    }
  }

  public function fetch_item($key)
  {
    $cache_file = \Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_PHP'.md5($key);

    if (is_file($cache_file)) {
      $test = include $cache_file;

      if ( ! is_array($test)) {
        return @unlink($path);
      } elseif (time() < $test[0]) {
        return $test[1];
      }
      @unlink($cache_file);
    }
    return FALSE;
  }

  public function store_item($key, $set = array(), $ttl = 0)
  {
    $cache_file = \Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_PHP'.md5($key);

    $vars = var_export($set, TRUE);
    $code = '<' . '?php return array(' . (time() + $ttl) . ", $vars);";

    return file_put_contents($cache_file, $code);
  }

  public function delete_item($key)
  {
    $cache_file = \Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_PHP'.md5($key);

    if (is_file($cache_file)) {
      return @unlink($cache_file);
    }
  }

  public function check_item($key)
  {
    return $this->fetch_item($key) !== FALSE;
  }

}

