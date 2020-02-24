<?php
require_once(__DIR__ . '/vendor/autoload.php');

function save($type, $empas) {
  switch ($type) {
    case 'DIE':
      $file   = fopen('die.txt', 'a');
    break;
    case 'LIVE':
      $file   = fopen('live.txt', 'a');
    break;
    case 'TIMEOUT':
      $file   = fopen('timeout.txt', 'a');
    break;
    case 'BAD':
      $file   = fopen('400.txt', 'a');
    break;
    default:
      $file   = fopen('unknown.txt', 'a');
    break;
  }

  fwrite($file, $empas . PHP_EOL);
  fclose($file);
}

use \RollingCurl\RollingCurl;

$rollingCurl = new \RollingCurl\RollingCurl();

$list     = explode("\n", str_replace("\r", "", file_get_contents('list.txt')));
foreach ($list as $key => $mp) {

  if (empty($mp)) { continue; }

  $rollingCurl->get('https://memexque.herokuapp.com/a.php?empas=' . $mp);
}

$rollingCurl
    ->setCallback(function(\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {

      parse_str(parse_url($request->getUrl(), PHP_URL_QUERY), $params);

      $json      =  json_decode($request->getResponseText());
      if (isset($json->status) && isset($json->empas)) {
        save($json->status, $json->empas);
        echo $request->getResponseText() . PHP_EOL;
      } else {
        save('BAD', $params['empas']);
      }
    })
    ->setSimultaneousLimit(5)
    ->execute();
;
