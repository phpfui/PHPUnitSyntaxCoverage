<?php

\error_reporting(E_ALL);

\ini_set('memory_limit', '-1');

\error_reporting(E_ALL);

// allow the autoloader to be included from any script that needs it.
function autoload(string $className) : void
  {
  $path = \str_replace('\\', DIRECTORY_SEPARATOR, __DIR__ . "/fixtures/{$className}.php");

  @include_once $path;
  }

\spl_autoload_register('autoload');

require_once 'vendor/autoload.php';
