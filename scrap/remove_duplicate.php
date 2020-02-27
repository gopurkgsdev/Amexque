<?php

ini_set("memory_limit", "-1");
set_time_limit(0);
error_reporting(0);

$list   = explode(PHP_EOL, file_get_contents('socks.txt'));
$list   = array_unique($list);

$file   = fopen('new_sock.txt', 'a');

foreach ($list as $key => $sock) {
  $mask       = " %-15.30s %30.30s\n";
  printf($mask, $key+1, $sock);
  fwrite($file, $sock . PHP_EOL);
}

fclose($file);
