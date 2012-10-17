<?php

namespace Cashier\Driver;

class SQLite
{

  private static $obj = NULL;



  public function free_all()
  {
    static::instance()->exec('DELETE FROM "data"');
  }

  public function fetch_item($key)
  {
    $key  = md5($key);

    $sql  = 'SELECT value FROM "data"';
    $sql .= "\nWHERE \"key\" = '$key'";

    if ($tmp = static::instance()->query($sql)) {
      $test = @array_shift($tmp->fetchArray(SQLITE3_NUM));

      if (\Cashier\Helpers::is_serialized($test)) {
        return unserialize($test);
      }
      static::delete_item($key);
    }
    return FALSE;
  }

  public function store_item($key, $val, $max)
  {
    $key  = md5($key);
    $time = time() + $max;
    $val  = str_replace("'", "''", serialize($val));

    $sql  = 'REPLACE INTO "data"';
    $sql .= '("key", "value", "expire")';
    $sql .= "\nVALUES('$key', '$val', $time)";

    return static::instance()->exec($sql);
  }

  public function delete_item($key)
  {
    $key  = md5($key);

    $sql  = 'DELETE FROM "data"';
    $sql .= "\nWHERE \"key\" = '$key'";

    return static::instance()->exec($sql);
  }

  public function check_item($key)
  {
    $key  = md5($key);

    $sql  = "SELECT COUNT(*) FROM \"data\"";
    $sql .= "\nWHERE \"key\" = '$key'";

    $tmp  = static::instance()->query($sql);

    return @array_shift($tmp->fetchArray(SQLITE3_NUM)) > 0;
  }


  private static function instance()
  {
    if ( ! static::$obj) {
      $db_file = \Cashier\Config::get('cache_dir').DIRECTORY_SEPARATOR.'__CACHE_DB';

      if ( ! is_file($db_file)) {
        touch($db_file);

        $tmp = new \SQLite3($db_file);

        $tmp->exec('CREATE TABLE "data"('
                  . '"key" CHAR(32) PRIMARY KEY,'
                  . '"value" TEXT,'
                  . '"expire" INTEGER'
                  . ')');

        $tmp->close();
        unset($tmp);
      }

      static::$obj = new \SQLite3($db_file);


      $time = time();

      $sql  = 'DELETE FROM "data"';
      $sql .= "\nWHERE \"expire\" < $time";

      static::$obj->exec($sql);
    }

    return static::$obj;
  }

}
