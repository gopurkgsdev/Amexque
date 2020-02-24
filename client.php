<?php
require_once(__DIR__ . '/vendor/autoload.php');

use \RollingCurl\RollingCurl;

$rollingCurl = new \RollingCurl\RollingCurl();

$list     = explode("\n", str_replace("\r", "", file_get_contents('list.txt')));
foreach ($list as $key => $mp) {

  if (empty($mp)) { continue; }

  $rollingCurl->get('https://private-api.kgsdev.com/KONTOL/a.php?empas=' . $mp);
}

$rollingCurl
    ->setCallback(function(\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {
      echo $request->getResponseText();
    })
    ->setSimultaneousLimit(50)
    ->execute();
;
