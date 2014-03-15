<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/15/14
 * Time: 12:09 PM
 */

class DB  {
    private $db;
    public function __construct(){
        $config = require_once('../config/default.php');
        $this->db = new PDO('mysql:host=localhost;dbname='.$config['db']['dbname'], $config['db']['login'], $config['db']['pass']);


        //parent::__construct($dns, $config['db']['login'], $config['db']['pass']);

    }

    public function insertCSVDataToDB($file, $closed = false){
        require_once('../library/CSV');
        $csvReader = new CsvReader($filePath);



    }
} 