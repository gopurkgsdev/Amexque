<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use Curl\Curl;

$curl   =  new Curl();

$curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, TRUE);
$curl->setOpt(CURLOPT_PROXY, $_REQUEST['sock']);
$curl->setOpt(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
$curl->setOpt(CURLOPT_TIMEOUT_MS, 5000);
$curl->setOpt(CURLOPT_USERAGENT, 'KONTOL');

$check  = $curl->get('https://www.cloudflare.com/cdn-cgi/trace');
if (!$check) {

  $file     = fopen('sock_die.txt', 'a');

  echo $_REQUEST['sock'] . ' DIE' . PHP_EOL;
} else {

  $file     = fopen('sock_live.txt', 'a');

  echo $_REQUEST['sock'] . ' LIVE' . PHP_EOL;
}

fwrite($file, $_REQUEST['sock'] . PHP_EOL);
fclose($file);
