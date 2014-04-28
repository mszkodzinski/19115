<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/15/14
 * Time: 7:23 PM
 */

include_once('../application/autoloader.php');
$db  = new Reader_Data();

var_dump($db->getCoords(NULL));