<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

error_reporting(0);
require_once(__DIR__ . '/../vendor/autoload.php');

use Curl\Curl;

$curl   =  new Curl();


for ($i=0; $i < 10; $i++) {
  $curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, TRUE);
  $curl->setOpt(CURLOPT_PROXY, '199.189.86.111:9800');
  $curl->setOpt(CURLOPT_PROXYUSERPWD, '2a50df0561e46e7cddd813cf25fc5ef5:9b34940124351ce52f6bf24d97b5be84');
  // $curl->setOpt(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
  $curl->setOpt(CURLOPT_TIMEOUT_MS, 10000);
  $curl->setOpt(CURLOPT_USERAGENT, 'KONTOL');
  $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
  $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
  $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);

  $check  = $curl->get('https://www.cloudflare.com/cdn-cgi/trace');
  echo $check . PHP_EOL;
}
die();
if (!$check) {

  $file     = fopen('sock_die.txt', 'a');

  echo $_REQUEST['sock'] . ' DIE' . PHP_EOL;
} else {

  $file     = fopen('sock_live.txt', 'a');

  echo $_REQUEST['sock'] . ' LIVE' . PHP_EOL;
}

fwrite($file, $_REQUEST['sock'] . PHP_EOL);
fclose($file);
