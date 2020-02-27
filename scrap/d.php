<?php

require_once(__DIR__ . '/../vendor/autoload.php');
use \Curl\Curl;

$curl   = new Curl();

$get    = $curl->get('http://www.sslproxies24.top/');
$file   = fopen('url.txt', 'a');

preg_match_all("/<a href='(.*)#more/i", $get, $matches);
foreach ($matches[1] as $key => $url) {
  fwrite($file, $url . PHP_EOL);
  echo $url . PHP_EOL;
}
fclose($file);
