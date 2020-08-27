<?php
error_reporting(E_ALL);

function classNameExists(string $className) : string
  {
  $path = __DIR__ . "/{$className}.php";
  $path = str_replace('\\', '/', $path);
  return file_exists($path) ? $path : '';
  }

function autoload($className) : void
  {
  $path = classNameExists($className);

  if ($path)
    {
    /** @noinspection PhpIncludeInspection */
    include $path;
    }
  }
spl_autoload_register('autoload');

require_once 'vendor/autoload.php';
