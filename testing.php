<?php

require_once(__DIR__ . '/vendor/autoload.php');
use Curl\Curl;

$curl   =  new Curl();

$curl->setHeader('Accept', 'application/json, text/javascript, */*; q=0.01');
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setHeader('Content-Type', 'application/json');
$curl->setHeader('Origin', 'https://travel.americanexpress.com');
$curl->setHeader('Referer', 'https://travel.americanexpress.com/travel/partner/subNav/login');

$postAmex   = $curl->post('https://travel.americanexpress.com/travel/partner/authenticate', '{"userId":"'.$username.'","password":"'.$password.'","requestSource":"https://www.amextravel.com/api/user?redirect=https%3A%2F%2Fwww.amextravel.com%2Ffeatured-hotel-searches","clientId":""}');
