<?php

namespace Cashier\Driver;

class Filesystem
{

  public function free_all()
  {
    foreach (glob(\Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_FILE*') as $cache_file) {
      @unlink($cache_file);
    }
  }

  public function fetch_item($key)
  {
    $cache_file = \Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_FILE'.md5($key);

    if (is_file($cache_file)) {
      $test   = @gzuncompress(file_get_contents($cache_file));
      $offset = strpos($test, '|');

      $old = (int) substr($test, 0, $offset);
      $new = substr($test, $offset + 1);

      if (($old - time()) <= 0) {
        @unlink($cache_file);
      }

      if (\Cashier\Helpers::is_serialized($new)) {
        return unserialize($new);
      }
    }
    return FALSE;
  }

  public function store_item($key, $val, $max)
  {
    $cache_file = \Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_FILE'.md5($key);
    $binary     = (time() + $max) . '|' . serialize($val);

    return file_put_contents($cache_file, @gzcompress($binary));
  }

  public function delete_item($key)
  {
    if (is_file($cache_file = \Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_FILE'.md5($key))) {
      return @unlink($cache_file);
    }
  }

  public function check_item($key)
  {
    return is_file(\Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_FILE'.md5($key));
  }

}
