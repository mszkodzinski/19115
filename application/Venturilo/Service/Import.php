<?php

class Venturilo_Service_Import
{
    const STATIONS_XML_URL = 'http://nextbike.net/maps/nextbike-official.xml?city=210';


    /**
     * @var bool
     */
    protected $_isDebug = true;


    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->_isDebug === true;
    }

    /**
     * @param $isDebug
     */
    public function setIsDebug($isDebug)
    {
        $this->_isDebug = $isDebug;
    }

    /**
     * @param $msg
     */
    public function log($msg)
    {
        if ($this->_isDebug) {
            echo $msg . "\r\n";
        }
    }

    public function loadStations() {
        $stations = $this->processXmlToArray(self::STATIONS_XML_URL);

        $stationsModel = new Venturilo_Model_Stations();
        $stationsModel->query("TRUNCATE TABLE stations");

        $stations = $this->shiftArrayUp($stations['country']['city']['place']);

        $stationsModel->insert($stations);
    }

    public function processXmlToArray($url) {
        $xmlstring = file_get_contents($url);

        $xml = simplexml_load_string($xmlstring);
        $json = json_encode($xml);
        return json_decode($json,TRUE);
    }

    private function shiftArrayUp($arr) {
        return array_map(function ($e) {
            return $e['@attributes'];
        }, $arr);
    }

    /**
     *
     */
    public function process()
    {
        $rideModel = new Venturilo_Model_Ride();

        $path = '../data/file/';
        $fileName = 'ListopadRAW.csv';

        $data = $this->_parseCSV($path.$fileName);

        $rideModel->insert($data);

    }

    /**
     * @param $fileName
     * @return array|bool
     */
    protected function _parseCSV($fileName)
    {
        if (!file_exists($fileName) || !is_readable($fileName)) {
            return false;
        }

        $header = NULL;
        $data = array();
        if (($handle = fopen($fileName, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, ';')) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        $this->log(' //----------------------------------------------------------------');
        $this->log(' Dla pliku ' . $fileName . ' zaimportowano dane ');
        $this->log(' //----------------------------------------------------------------');
        return $data;
    }


}
