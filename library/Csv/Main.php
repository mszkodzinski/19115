<?php
class Csv_Main
{
    const ERR_FILE_PATH_EMPTY = -10;
    const ERR_FILE_NOT_EXISTS = -20;
    const ERR_FILE_NOT_READABLE = -30;
    const ERR_FILE_NOT_WRITABLE = -40;
    const ERR_FILE_NOT_OPENED = -50;
    const FILE_READ_MODE = 'rb';
    const FILE_WRITE_MODE = 'a+';

    /**
     * Pełna sciezka do pliku
     * @var string
     */
    protected $_filePath = '';

    /**
     * Znak delimitera (rozdzielający kolejne pola w CSV)
     * @var string
     */
    protected $_delimiter = ';';

    /**
     * Znak otaczajacyy watosci typu string
     * @var type
     */
    protected $_strEnclosure = '"';

    /**
     * Znak escape-ujący znaki specjalne w stringu
     * @var type
     */
    protected $_strEscape = '\\';

    /**
     * Handler otwartego pliku
     * @var mixed
     */
    protected $_fileHandler = false;

    /**
     * Kodowanie znaków na wejściu
     * @var string
     */
    protected $_inputEncoding = 'UTF-8';

    /**
     * Kodowanie znaków docelowe
     * @var string
     */
    protected $_outputEncoding = 'UTF-8';

    /**
     * Locales do czytania plików CSV
     * @var type
     */
    protected $_locales = 'pl_PL.UTF8';

    /**
     * Wskaznik konca pliku
     * @var boolean
     */
    protected $_eof;

    /**
     * Mapowanie kolumn (kol_no => kol_nazwa)
     * @var array
     */
    protected $_colMapping;


    /**
     * @param null $fileName
     */
    public function __construct($fileName = null)
    {
// Bo warto zainicjować standardy
        $this->_eof = false;
        $this->_colMapping = false;
        $this->setLocales($this->_locales);

        if ($fileName) {
            $this->_filePath = $fileName;
        }
    }

    public function __destruct()
    {
        $this->_closeFile();
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath = '')
    {
        $this->_filePath = $filePath;
    }

    /**
     * @param string $delimiter
     * @return bool
     */
    public function setDelimiter($delimiter = '')
    {
        if (!empty($delimiter)) {
            $this->_delimiter = $delimiter;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $strEnc
     * @return bool
     */
    public function setStrEnclosure($strEnc = '')
    {
        if (!empty($strEnc)) {
            $this->_strEnclosure = $strEnc;
        } else {
            return false;
        }
    }

    /**
     * @param string $strEscape
     * @return bool
     */
    public function setStrEscape($strEscape = '')
    {
        if (!empty($strEscape)) {
            $this->_strEscape = $strEscape;
        } else {
            return false;
        }
    }

    /**
     * @param $encoding
     */
    public function setInputEncoding($encoding)
    {
        $this->_inputEncoding = $encoding;
    }

    /**
     * @param $encoding
     */
    public function setOutputEncoding($encoding)
    {
        $this->_outputEncoding = $encoding;
    }

    /**
     * @param string $locales
     */
    public function setLocales($locales='')
    {
        $this->_locales = $locales;
        setlocale(LC_ALL, $locales);
    }

    /**
     * @return bool
     */
    public function detectEncodingFromFile()
    {
        if (!$this->_fileHandler && !$this->_openFile(self::FILE_READ_MODE)) {
            return false;
        }

// Odczyt 2 pierwszych bajtów w celu wyczajenia ewentualnego kodowania UTF-16
        rewind($this->_fileHandler);
        $first2Bytes = fread($this->_fileHandler, 2);
        rewind($this->_fileHandler);
        $first3Bytes = fread($this->_fileHandler, 3);

        if($first2Bytes === chr(0xff).chr(0xfe)) {
            $this->_inputEncoding = 'UTF-16LE';
        } elseif ($first2Bytes === chr(0xfe).chr(0xff)) {
            $this->_inputEncoding = 'UTF-16BE';
        } elseif ($first3Bytes === chr(0xef).chr(0xbb).chr(0xbf)) {
            $this->_inputEncoding = 'UTF-8';
        } else {
// Jak nie znalezione przez BOM - szukamy metodami tradycyjnymi
            rewind($this->_fileHandler);
            $textProbe = fread($this->_fileHandler, 20000);
            $this->_inputEncoding = strtoupper(mb_detect_encoding($textProbe));
        }

        if ($this->_inputEncoding != 'UTF-8' && $this->_inputEncoding != 'UTF8') {
            $this->_convertFile();
        }

        rewind($this->_fileHandler);
    }

    private function _convertFile()
    {
        $this->_closeFile();

        $convExec = '/usr/bin/iconv -t ' . $this->_outputEncoding . ' -o ' . $this->_filePath . ' ' . $this->_filePath;
        exec( $convExec );
        $this->_inputEncoding = "UTF-8";
        $this->_openFile(self::FILE_READ_MODE);
    }

    /**
     * @param array $colMapping
     */
    public function setColMappings(array $colMapping)
    {
        $this->_colMapping = $colMapping;
    }

    /**
     * @param string $readMode
     * @return int
     */
    protected function _openFile($readMode = '')
    {
// Zamkniecie pliku - tak żeby się nie motał
        $this->_closeFile();

        if (empty($this->_filePath)) {
            return self::ERR_FILE_PATH_EMPTY;
        }

        if (!is_file($this->_filePath) || !file_exists($this->_filePath)) {
            return self::ERR_FILE_NOT_EXISTS;
        }

        if ($readMode == self::FILE_READ_MODE && !is_readable($this->_filePath)) {
            return self::ERR_FILE_NOT_READABLE;
        } elseif($readMode == self::FILE_WRITE_MODE && !is_writable($this->_filePath)) {
            return self::ERR_FILE_NOT_WRITABLE;
        }

        $this->_fileHandler = fopen($this->_filePath, $readMode);
        if (!$this->_fileHandler) {
            return self::ERR_FILE_NOT_OPENED;
        }
    }

    protected function _closeFile()
    {
        if ($this->_fileHandler) {
            fclose($this->_fileHandler);
            $this->_fileHandler = null;
        }
    }
}