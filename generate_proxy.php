<?php
$file   = fopen('sock.txt', 'a');
for ($i=4999; $i < 5100; $i++) {
  fwrite($file, '127.0.0.1:' . $i . PHP_EOL);
}
fclose($file);
