<?php

$file   = explode("\n", str_replace("\r", "", file_get_contents('list.txt')));
$file   = array_unique($file);

$open   = fopen('res.txt', 'a');

foreach ($file as $key => $value) {
  $username = explode(',', $value)[4];
  $password = explode(',', $value)[5];
  fwrite($open, $username . '|' . $password . PHP_EOL);
  echo $username . '|' . $password . PHP_EOL;
}

fclose($open);
