<?php
$file   = fopen('sock.txt', 'a');
for ($i=7000; $i < 7011; $i++) {
  fwrite($file, '127.0.0.1:' . $i . PHP_EOL);
}
fclose($file);
