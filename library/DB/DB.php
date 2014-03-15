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
        $this->db = new PDO('mysql:host=127.0.0.1;dbname='.$config['db']['dbname'], $config['db']['login'], $config['db']['pass']);


    }

    public function getDBObject() {
        return $this->db;
    }

    public function insertFile($file_name) {
        $query = $this->db->prepare('INSERT INTO `importer` (`file` ,`import_date` ,`status`) VALUES (?, CURRENT_TIMESTAMP ,  \'0\');');
        return $query->execute(array($file_name));
    }

    public function insertCSVDataToDB($filename, $closed = false){

        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, "\t")) !== FALSE)
            {
                if(!$header){
                    $header = $row;
                    var_Dump($header);
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }
        print_r($data);
        return $data;


        $ignoreFirstRow = 1;
        $csvReader = new Csv_Reader($filename);

// Zaczytanie danych z pliku
        $csvReader->discoverDelimiter();
        $csvReader->setInputEncoding('UTF8');
        $csvReader->detectEncodingFromFile();
        $csvReader->readColumnNames();
        $linesNo = $csvReader->getLineNo();
        echo $linesNo;
// Aby za każdym razem czytać plik od początku a nie gdzieś od środka
        $csvReader->rewindFile();

// Pobranie kolejnych linii z pliku i zapis do LS-a
        for ($lineNo = 0; $lineNo <= $linesNo; $lineNo++) { // W przypadku ignorowanie pierwszego wiersza - zaczynamy od 2giego
            // Jezeli jest potrzebne pominięcie pierwszej linni - wczytujemy ja ale pomijamy w imporcie
            if ($ignoreFirstRow == 1 && $lineNo == 0) {
                $csvReader->readLines();
            } else {
                $csvReader->readLines();
                $line = $csvReader->getLineData();

                // Czasem zdarzają się linie całkiem puste i takich nie chcemy.
                if (!empty($line)) {
                    if (is_array($line)) {
                        foreach ($line as $k => $v) {
                            $line[$k] = Core_Utils::clearNonUtf8Characters($v);
                        }
                    } else if (is_string($line)) {
                        $line = Core_Utils::clearNonUtf8Characters($line);

                    }
                    var_dump( $line);
                    // Zrób coś...
                }
            }
        }



    }
} 