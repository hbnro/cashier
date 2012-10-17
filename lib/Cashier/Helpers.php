<?php

namespace Cashier;

class Helpers
{

  public static function is_serialized($test)
  { // TODO: include WP url
    if ( ! is_string($test)) {
      return FALSE;
    }

    if ($test == 'N;') {
      return TRUE;
    } elseif ( ! preg_match('/^([adObis]):/', $test, $match)) {
      return FALSE;
    }

    switch ($match[1]) {
      case 'a'; case 'O'; case 's';
      if (preg_match("/^{$match[1]}:[0-9]+:.*[;}]\$/s", $test)) {
          return TRUE;
        }
      break;
      case 'b'; case 'i'; case 'd';
        if (preg_match("/^{$match[1]}:[0-9\.E-]+;\$/", $test)) {
          return TRUE;
        }
      break;
      default; break;
    }

    return FALSE;
  }

}
