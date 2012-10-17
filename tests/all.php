<?php

date_default_timezone_set('UTC');

require dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

Cashier\Config::set('cache_dir', '/tmp');
Cashier\Config::set('driver', 'sqlite');
#Cashier\Config::set('driver', 'filesystem');


$r = rand(3, 12);

if (Cashier\Base::begin()) {
  echo "This text will be cached for $r secs. (" . uniqid('') . ")\n";
  Cashier\Base::end($r);
}


Cashier\Base::block('foo', 5, function () {
  echo "And this by 5 secs (" . date('H:i:s') . ").\n";
});
