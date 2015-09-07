<?php
error_reporting(E_ALL);
ini_set("display_errors", "on");
set_error_handler(function ($severity, $message, $file, $line) {
    throw new \ErrorException($message, $severity, $severity, $file, $line);
});

chdir(dirname(__DIR__));

require 'vendor/autoload.php';
require 'src/bootstrap.php';
