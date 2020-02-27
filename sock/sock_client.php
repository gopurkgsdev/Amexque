<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

error_reporting(0);

require_once(__DIR__ . '/../vendor/autoload.php');
use \RollingCurl\RollingCurl;

$rollingCurl  = new \RollingCurl\RollingCurl();

$sock         = array_unique(explode("\n", str_replace("\r", "", file_get_contents('sock.txt'))));

echo PHP_EOL . '  ' . count($sock) . '  ' . PHP_EOL;
echo '  Continue?  /Enter : ';
$input['list']       = trim(fgets(fopen("php://stdin", "r")));

foreach ($sock as $key => $val) {
  if (empty($val)) { continue; }

  $rollingCurl->get('http://localhost/Amexque/sock/sock_server.php?sock=' . $val);
}

$rollingCurl
    ->setCallback(function(\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {
      echo $request->getResponseText();
    })
    ->setSimultaneousLimit(1000)
    ->execute();
;
