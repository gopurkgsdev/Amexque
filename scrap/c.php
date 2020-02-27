<?php

require_once(__DIR__ . '/../vendor/autoload.php');
use \Curl\Curl;

$curl   = new Curl();
$file   = fopen('url.txt', 'a');

for ($i=0; $i < 200; $i++) {
  $get    = $curl->get('https://pastebin.com/u/PremSocks/1');
  preg_match_all('/<a href="(.*)">SOCKS/i', $get, $matches);

  foreach ($matches[1] as $key => $value) {
    fwrite($file, 'https://pastebin.com' . $value . PHP_EOL);
    echo 'https://pastebin.com' . $value . PHP_EOL;
  }
}
fclose($file);
