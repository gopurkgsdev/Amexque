<?php
error_reporting(0);
require_once(__DIR__ . '/../vendor/autoload.php');
use \RollingCurl\RollingCurl;

$list   = explode(PHP_EOL, file_get_contents('url.txt'));
$list   = array_unique($list);

$rollingCurl = new \RollingCurl\RollingCurl();

foreach ($list as $key => $url) {

  if (empty($url)) { continue; }

  $rollingCurl->get($url . '?url=' . $url);
}

$rollingCurl
    ->setCallback(function(\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {
      parse_str(parse_url($request->getUrl(), PHP_URL_QUERY), $params);

      $file   = fopen('socks.txt', 'a');
      preg_match_all('/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b:\d{2,5}/i', $request->getResponseText(), $matches);
      foreach ($matches[0] as $key => $sock) {
        fwrite($file, $sock . PHP_EOL);
      }
      fclose($file);

      $mask       = " %-90.100s %-15.30s %10.30s\n";
      printf($mask, $params['url'], 'Result: ' . count($matches[0]), ' Found');
    })
    ->setSimultaneousLimit(1000)
    ->execute();
;
