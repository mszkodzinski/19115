<?php
setlocale(LC_CTYPE, "pl.UTF8");

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit ( 300 );
include_once('../application/autoloader.php');
$db  = new DB_DB();
$mapper  = new DB_CSVToDBMapper($db);

//$file = '/var/www/19115/data/file/2014-03-15.csv';
//$fileconv = '/var/www/19115/data/file/2014-03-15_utf8.csv';

$path = '../data/file/';

$query = $db->getDBObject()->query('SELECT * FROM  `importer` where status = 0 order by id asc ');

foreach($query->fetchAll() as $qr) {
    $file = $path.$qr['file'];
    $fileconv = substr($file, 0, strlen($file)-4).'_utf8.csv';
    shell_exec('iconv -f UTF16LE -t UTF8 '.$file.' > '.$fileconv);
    $mapper->insertCSVDataToDB($fileconv);

    $db->updateRow('`importer`', array('status' => '1'), array('id' => $qr['id']));
}






