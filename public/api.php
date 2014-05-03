<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);


include_once('../application/autoloader.php');

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : 'test');
$data = $_POST;
if (!count($data)) {
    $data = $_GET;
}

$engine = new Api_Engine();
echo $engine->run($action, $data);