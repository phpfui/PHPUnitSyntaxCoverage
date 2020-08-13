<?php

error_reporting(E_ALL);

$vendorDir = __DIR__ . '/../../vendor';

if (file_exists($file = $vendorDir . '/autoload.php')) {
    require_once $file;
} elseif (file_exists($file = './vendor/autoload.php')) {
    require_once $file;
} else {
    throw new \RuntimeException('Composer autoload file not found');
}
