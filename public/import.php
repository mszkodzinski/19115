<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('../application/autoloader.php');

$db = new DB_DB();
$db->insertCSVDataToDB('/var/www/19115/data/file/2014-03-15.csv');

