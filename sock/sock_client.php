<?php

require_once(__DIR__ . '/../vendor/autoload.php');
use \RollingCurl\RollingCurl;

$rollingCurl  = new \RollingCurl\RollingCurl();

$sock         = array_unique(explode("\n", str_replace("\r", "", file_get_contents('sock.txt'))));
foreach ($sock as $key => $val) {
  if (empty($val)) { continue; }

  $rollingCurl->get('http://localhost/Amexque/sock/sock_server.php?sock=' . $val);
}

$rollingCurl
    ->setCallback(function(\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {
      echo $request->getResponseText();
    })
    ->setSimultaneousLimit(100)
    ->execute();
;
