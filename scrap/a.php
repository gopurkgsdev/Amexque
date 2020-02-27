<?php
require_once(__DIR__ . '/../vendor/autoload.php');
use \Curl\Curl;

function getString($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$curl   = new Curl();

$file   = fopen('url.txt', 'a');

for ($i=0; $i < 2000; $i++) {
  $get    = $curl->get('https://cse.google.com/cse/element/v1?rsz=filtered_cse&num=10&hl=en&source=gcsc&gss=.com&start='.$i.'&cselibv=8b2252448421acb3&cx=013305635491195529773:0ufpuq-fpt0&q=socks%20proxy&safe=off&cse_tok=AJvRUv0UdjuTBQIFwOfHAOUqUOun:1582755268229&sort=&exp=csqr,cc&callback=google.search.cse.api9045');
  $result = json_decode('{'.getString($get, '({', '})').'}');
  foreach ($result->results as $key => $url) {
    fwrite($file, $url->formattedUrl . PHP_EOL);
    echo $url->formattedUrl . PHP_EOL;
  }
}
fclose($file);
