<?php

namespace Cashier\Driver;

class Memcache
{

  private static $res = NULL;

  public function free_all()
  {
    memcache_flush(cache::link());

    $end = time() + 1;

    while (time() < $end);
  }

  public function fetch_item($key)
  {
    return memcache_get(cache::link(), $key);
  }

  public function store_item($key, $val, $max)
  {
    return memcache_set(cache::link(), $key, $val, 0, $max);
  }

  public function delete_item($key)
  {
    return memcache_delete(cache::link(), $key);
  }

  public function check_item($key)
  {
    // http://www.php.net/manual/en/memcache.getextendedstats.php#98161
    $list  = array();
    $slabs = memcache_get_extended_stats(cache::link(), 'slabs');
    $items = memcache_get_extended_stats(cache::link(), 'items');

    foreach ($slabs as $server) {
      foreach (array_keys($server) as $id) {
        $test = memcache_get_extended_stats('cachedump', (int) $id);

        foreach ($test as $keys) {
          foreach (array_keys($keys) as $one) {
            if ($one === $key) {
              return TRUE;
            }
          }
        }
      }
    }

    return FALSE;
  }

  private static function link()
  {
    if (! static::$res) { // TODO: allow configuration
      static::$res = memcache_connect('localhost', '11211');
    }

    return static::$res;
  }

}
