<?php

namespace Cashier\Driver;

class APC
{

  public function free_all()
  {
    apc_clear_cache('user');
    apc_clear_cache();
  }

  public function fetch_item($key)
  {
    return apc_fetch($key);
  }

  public function store_item($key, $val, $max)
  {
    return apc_store($key, $val, $max);
  }

  public function delete_item($key)
  {
    return apc_delete($key);
  }

  public function check_item($key)
  {
    return apc_exists($key);
  }

}
