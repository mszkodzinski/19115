<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/15/14
 * Time: 12:09 PM
 */

class DB extends PDO  {
    private $db;
    public function __construct(){
        $config = require_once('../config/default.php');
        //$this->db = mysql_connect($config['db']['host'],$config['db']['login'],$config['db']['pass']);


        //parent::__construct($dns, $config['db']['login'], $config['db']['pass']);

    }

    public function insertData($data){

       // $this->db->
    }
} 