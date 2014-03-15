<?php
setlocale(LC_CTYPE, "pl.UTF8");

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('../application/autoloader.php');
$db  = new DB_DB();

$file = '/var/www/19115/data/file/2014-03-15.csv';
$fileconv = '/var/www/19115/data/file/2014-03-15_utf8.csv';
shell_exec('iconv -f UTF16LE -t UTF8 '.$file.' > '.$fileconv);

$mapper  = new DB_CSVToDBMapper($db);
$mapper->insertCSVDataToDB($fileconv);

