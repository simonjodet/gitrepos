<?php
require_once __DIR__ . '/../vendor/autoload.php';
$loader = new \Mockery\Loader;
$loader->register();
putenv('APP_ENV=test_cases');