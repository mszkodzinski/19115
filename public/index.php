<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('../application/autoloader.php');

$accountData = array(
    'username' => 'cccc',
    'password' => 'ttttt',
    'server' => 'imap.gmail.com',
    'port' => '993',
    'secure' => 'SSL',
);
$connection = new Imap_Connection_Horde($accountData);
var_dump($connection->isCorrect());
//phpinfo();