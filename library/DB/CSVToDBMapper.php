<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/15/14
 * Time: 2:54 PM
 */

class DB_CSVToDBMapper {
    protected $db = null;
//    public $header_mapper = array(
//        'number',
//        'date_of_acceptance',
//        'k_status'=>array('insertDictData','status'),
//        'district',
//        'street',
//        'house_nr',
//        'lattitude',
//        'longtitude',
//        'k_organization'=>array('insertDictData','organization'),
//        'close_date',
//        'k_source'=>array('insertDictData','source')
//    );

    public function __construct(DB_DB $db){
        $this->db = $db;

    }


    public function prepareCSVRow($row, $header_mapper){

        $row_to_db = array();
        foreach($row as $key=>$val){
            if(array_key_exists($key, $header_mapper)){
               // $func = $val[0];
                //$id = call_user_func_array(array($this->db,$val[0] ), array($header_mapper[$key][1], $val));
                $id = $this->db->insertDictData($header_mapper[$key][1], $val);
                $row_to_db[$key]=$id;
            } else {
                $row_to_db[$key]=is_null($val)?'null': iconv('UTF-16LE', 'UTF-8', $val."\0") ; ;
            }

        }
        var_dump($row_to_db);
        return $row_to_db;

    }

    public function insertCSVDataToDB($filename, $closed = false){

        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header_mapper = array(
            'number',
            'date_of_acceptance',//=>array('dateParser'),
            'k_status'=>array('insertDictData','status'),
            'k_district'=>array('insertDictData','district'),
            'street',
            'house_nr',
            'lattitude',
            'longtitude',
            'k_organization'=>array('insertDictData','organization'),
            'close_date',//=>array('dateParser'),
            'k_source'=>array('insertDictData','source')
        );

        $headers = array();
        $i = 0;
        foreach ($header_mapper as $key=>$val){
            if(is_array($val)){
                $headers[$i] = $key;

            }else{
                $headers[$i] = $val;
            }
            $i++;

        }

        var_dump($headers);

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, "\t")) !== FALSE)
            {
                if(!$header){
//                    var_dump($header);
                    $header = $row;
                    //var_Dump($header);
                } else {
                    $tmp = array_combine($headers, $row);
                    $row =  $this->prepareCSVRow($tmp, $header_mapper);
                    $this->db->insertRowData('notification', $row);
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }
        print_r($data);
        return $data;


    }
} 