<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);


include_once('../application/autoloader.php');

$action = $_GET['action'];
$data = $_POST;
if (!count($data)) {
    $data = $_GET;
}

$engine = new Api_Engine();
echo $engine->action($action, $data);