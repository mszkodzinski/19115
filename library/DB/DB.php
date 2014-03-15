<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/15/14
 * Time: 12:09 PM
 */

class DB_DB  {
    private $db;
    public function __construct(){
        $config = require_once('../config/default.php');
        $this->db = new PDO('mysql:host=localhost;dbname='.$config['db']['dbname'], $config['db']['login'], $config['db']['pass']);


    }

    public function getDBObject() {
        return $this->db;
    }

    public function insertFile($file_name) {
        $query = $this->db->prepare('INSERT INTO `importer` (`file` ,`import_date` ,`status`) VALUES (?, CURRENT_TIMESTAMP ,  \'0\');');
        return $query->execute(array($file_name));
    }

    public function selectData($table, $name){
        $stmt = $this->db->prepare('select id from '.$table.' where name =?');
        $res = $stmt->execute(array($name));
        return $stmt->fetch();

    }

    public function insertData($table, $name){
        try {
            $stmt = $this->db->prepare('insert IGNORE  into '.$table.' (name) values(?)');
            try {
                $this->db->beginTransaction();
                $stmt->execute( array( $name));
                $this->db->commit();
                return $this->db->lastInsertId();
            } catch(PDOExecption $e) {
                $this->db->rollback();
                print "Error!: " . $e->getMessage() . "</br>";
            }
        } catch( PDOExecption $e ) {
            print "Error!: " . $e->getMessage() . "</br>";
        }
        return null;
    }

    public function insertDictData($table, $name){
        if($res = $this->selectData($table,$name)){
            return $res['id'];
        }else{
            return $this->insertData($table, $name);
        }
    }


    public function insertRowData($table, $row){
        $cols_no = count($row);
        $str = '';
        for($j=0; $j<$cols_no; $j++){
            $str .='?,';
        }


        try {
            $sql ='insert  into '.$table.' ('.implode(',', array_keys($row)).' ) values ('.trim($str,',').')';
            echo $sql;
            $stmt = $this->db->prepare($sql);
            try {
                $this->db->beginTransaction();
                $stmt->execute(array_values($row));
                $this->db->commit();
                return $this->db->lastInsertId();
            } catch(PDOExecption $e) {
                $this->db->rollback();
                var_dump($e);
                print "Error!: " . $e->getMessage() . "</br>";
                return false;
            }
        } catch( PDOExecption $e ) {
            var_dump($e);
            print "Error!: " . $e->getMessage() . "</br>";
            return false;
        }
        return null;


    }


} 