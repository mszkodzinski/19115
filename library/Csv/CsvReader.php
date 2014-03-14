<?php
/**
 * Reader class for CSV
 * @example
 *      $csvReader = new Reader();
 *      $csvReader->setFilePath('file_to_read.csv');
 *      $csvReader->openFile();
 *      $lineNumber = $csvReader->getLineNo();
 *      $csvReader->readColumnNames();
 *      $ColNames = $csvReader->getColumnNames();
 *      $csvReader->setColMappings(array(0 => 'name', 1 => 'surname', 3 => 'email'));
 *      $csvReader->readLines(100);
 *      $result = $csvReader->getData();
 */
class CsvReader extends CSV
{
    /**
     * Dane wielu linii
     * @var mixed
     */
    private $_data;

    /**
     * Dane pojedynczcej linii
     * @var mixed
     */
    private $_lineData;

    /**
     * Nazwy kolumn pobrane z pliku
     * @var mixed
     */
    private $_columnNames;

    /**
     * Ilosc linii w pliku
     * @var mixed
     */
    private $_lineNo = false;

    /**
     * @param null $fileName
     */
    public function __construct($fileName = null)
    {
        // Automatyczne wykrywanie końca linii w plikach (szczególnie ważne dla plików ze zgniłego japca)
        ini_set("auto_detect_line_endings", true);

        if ($fileName) {
            parent::__construct($fileName);
            $this->openFile();
        }
    }

    /**
     * @return bool
     */
    public function openFile()
    {
        $this->_openFile(self::FILE_READ_MODE);
        return true;
    }

    /**
     * @return bool
     */
    public function closeFile()
    {
        $this->_closeFile();
        return true;
    }

    /**
     * @return int
     */
    public function readColumnNames()
    {
        if (!$this->_fileHandler) {
            return self::ERR_FILE_NOT_OPENED;
        }

        $this->rewindFile();
        $this->_readLine();
        $this->_columnNames = $this->_lineData;
        $this->_lineData = null;
        $this->rewindFile();
    }

    /**
     * @return mixed
     */
    public function getColumnNames()
    {
        return $this->_columnNames;
    }

    public function rewindFile()
    {
        rewind($this->_fileHandler);
        $this->_eof = false;
    }

    /**
     * @param int $linesNumber
     * @return int
     */
    public function readLines($linesNumber=1)
    {
        if (!$this->_fileHandler){
            return self::ERR_FILE_NOT_OPENED;
        }

        for ($lineNo=0; $lineNo < $linesNumber && !$this->_eof; $lineNo++) {
            $this->_readLine();
            if (is_array($this->_lineData) && count($this->_lineData)) {
                $this->_data[] = $this->_lineData;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getLineData()
    {
        return $this->_lineData;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     *
     * @return mixed
     */
    public function getLineNo()
    {
        if (!$this->_fileHandler) {
            return self::ERR_FILE_NOT_OPENED;
        }

        // Jako że operacja może okazać się czasochłonna
        // zakładamy że jeżeli zmienna zostałą obliczona
        // długość pliku się nie zmieniła jezeli nie - liczymy linie
        if (!$this->_lineNo) {
            $this->_lineNo = 0;
            rewind($this->_fileHandler);
            while(!feof($this->_fileHandler)) {
                $line = fgets($this->_fileHandler, 4096);
                $this->_lineNo++;
            }
            $this->_lineNo--;
            rewind($this->_fileHandler);
        }
        return $this->_lineNo;
    }

    /**
     * Proba wykrycia delimitera - zakłada  że w pierwszej linni bedzie ich duzo
     * a ze jest to wiersz z nagłówkami - będą one czyste, bez delimiterow obcych
     */
    public function discoverDelimiter()
    {
        if (!$this->_fileHandler) {
            return self::ERR_FILE_NOT_OPENED;
        }

        $delimiters = array(',' => 0, ';' => 0);

        rewind($this->_fileHandler);
        $line = fgets($this->_fileHandler, 20000);

        foreach ($delimiters AS $delim => $number) {
            $delimiters[$delim] = substr_count($line, $delim);
        }

        arsort($delimiters);
        $delimiters = array_keys($delimiters);
        $delimiter = array_shift($delimiters);
        $this->setDelimiter($delimiter);
        rewind($this->_fileHandler);
    }

    private function _readLine()
    {
        $lineData = fgetcsv($this->_fileHandler, 0, $this->_delimiter, $this->_strEnclosure, $this->_strEscape);

        if (feof($this->_fileHandler)) {
            $this->_eof = true;
        }

        if (is_array($lineData)) {
            foreach ($lineData AS $fieldKey => $field) {
                if ($this->_inputEncoding != $this->_outputEncoding) {
                    $lineData[$fieldKey] = trim(iconv($this->_inputEncoding, $this->_outputEncoding, $field));
                } else {
                    $lineData[$fieldKey] = trim($field);
                }
            }
        }

        if ($this->_colMapping && is_array($lineData)) {
            foreach ($this->_colMapping AS $colNo => $colName) {
                $this->_lineData[$colName] = $lineData[$colNo];
            }
        } else {
            $this->_lineData = $lineData;
        }
    }

    /**
     * @return bool
     */
    public function isEof()
    {
        return $this->_eof;
    }
}