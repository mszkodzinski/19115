<?php
setlocale(LC_CTYPE, "pl.UTF8");

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

include '../application/autoloader.php';

array_shift($argv);

$cli = new Core_Cli();
$cli->run(array_shift($argv), array_shift($argv), $argv);