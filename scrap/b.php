<?php
require_once(__DIR__ . '/../vendor/autoload.php');
use \Curl\Curl;

$file   = fopen('socks.txt', 'a');

$curl   = new Curl();
$get    = $curl->get($_REQUEST['url']);
preg_match_all('/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b:\d{2,5}/i', $get, $matches);

foreach ($matches[0] as $key => $sock) {
  fwrite($file, $sock . PHP_EOL);
}
fclose($file);

die(json_encode([
  'url'     =>  $_REQUEST['url'],
  'result'  =>  count($matches[0])
]));
