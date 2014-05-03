<?php

class p19115_Service_Import
{
    const MAIL_TITLE_CLOSE = 'Lista zamkniętych zgłoszeń z systemu CKM';
    const MAIL_TITLE_ALL = 'Lista zgłoszeń z systemu CKM';


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

    public function read()
    {
        $config = Config::get('mail');
        $connection = new Imap_Connection_Horde(array(
            'username' => $config['login'],
            'password' => $config['pass'],
            'server' => $config['host'],
            'port' => $config['port'],
            'secure' => 'ssl',
        ));
        if (!$connection->isCorrect()) {
            $this->log('Failed');
            return false;
        }

        // Pobierz wszystkie od daty... bez treści
        $fileModel = new p19115_Model_File();
        $newestFile = $fileModel->select('*', array('ORDER' => 'id DESC', 'LIMIT' => 1));
        if (!count($newestFile)) {
            $dateStart = '2013-01-01';
        } else {
            $dateStart = substr($newestFile[0]['file'], 0, 10);
        }

        foreach ($connection->getMailboxList(array('INBOX')) as $mailbox) {
            $uids = $mailbox->getMessagesUidSinceDate($dateStart);

            foreach ($mailbox->getMessagesByUid($uids, true) as $message) {
                $model = $message->getModel();
                foreach ($message->getAttachments() as $a) {
                    $fileName = substr($model['date'], 0, 10);
                    switch (trim($model['subject'])) {
                        case self::MAIL_TITLE_CLOSE:
                            $fileName .= '_closed';
                            break;
                        case self::MAIL_TITLE_ALL:
                            $fileName .= '_open';
                            break;
                    }
                    $fileName .= '.csv';

                    $a->saveContent('../data/file/' . $fileName);
                    $fileModel->insert(array(
                        'file' => $fileName,
                        'import_date' => 'CURRENT_TIMESTAMP',
                        'status' => p19115_Model_File::STATUS_NEW
                    ));
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function convertCoords()
    {
        $model = new p19115_Model_Notification();
        $result = $model->select(
            array('longtitude_2000', 'lattitude_2000'),
            array(
                'AND' => array(
                    'lattitude_2000[!]' => '',
                    'longtitude_2000[!]' => '',
                    'lattitude' => ''
                ),
                'ORDER' => 'id ASC'
            )
        );

        if (!$result) {
            return false;
        }echo count($result);

        $path = '../data/file/';
        $file = $path . 'coords.csv';
        $fp = fopen($file, 'w');
        foreach ($result as $coords) {
            foreach ($coords as $k => $v) {
                $coords[$k] = str_replace(',', '.', $v);
            }
            fputcsv($fp, array_values($coords), "\t");
        }
        fclose($fp);

        $file_out = $path . 'coords_converted.csv';
        $file_joined = $path . 'coords_joined.csv';

        exec("proj +proj=tmerc +lat_0=0 +lon_0=21 +k=0.999923 +x_0=7500000 +y_0=0 +ellps=GRS80 +units=m +no_defs -f %12.6f -I < $file > $file_out");
        exec("paste -d'\\t' $file $file_out > $file_joined");

        // Aby za każdym razem czytać plik od początku a nie gdzieś od środka
        if (($handle = fopen($file_joined, 'r')) === FALSE) {
            return false;
        }
        while (($row = fgetcsv($handle, 1000, "\t")) !== FALSE) {
            if (count($row) < 4) {
                continue;
            }
            $model->update(array(
                'lattitude' => trim($row[3]),
                'longtitude' => trim($row[2])
            ), array(
                'AND' => array(
                    'lattitude_2000' => trim($row[1]),
                    'longtitude_2000' => trim($row[0])
                )
            ));
        }
        fclose($handle);
    }

    /**
     *
     */
    public function process()
    {
        $fileModel = new p19115_Model_File();
        $db  = Medoo_Medoo::getInstance();

        $path = '../data/file/';

        // 'SELECT * FROM  `importer` where status = 0 order by id asc ');
        foreach ($fileModel->select('*', array('status' => p19115_Model_File::STATUS_NEW)) as $qr) {
            $file = $path.$qr['file'];
            $fileconv = substr($file, 0, strlen($file) - 4) . '_utf8.csv';
            $this->_convertFile($file, $fileconv);
            $this->_insertData($fileconv);

            //$db->updateRow('`importer`', array('status' => '1'), array('id' => $qr['id']));
            $db->update('importer', array('status' => p19115_Model_File::STATUS_PARSED), array('id' => $qr['id']));
        }
    }

    /**
     * @param $fileName
     * @param $fileConvName
     */
    protected function _convertFile($fileName, $fileConvName)
    {
        shell_exec('iconv -f UTF16LE -t UTF8 ' . $fileName . ' > ' . $fileConvName);
    }

    /**
     * @param $fileName
     * @return array|bool
     */
    protected function _insertData($fileName)
    {
        $closed = false;
        if (!file_exists($fileName) || !is_readable($fileName)) {
            return false;
        }

        $headerMapper = array(
            'number',
            'date_of_acceptance',//=>array('dateParser'),
            'k_status' => array('insertDictData', 'status'),
            'k_district' => array('insertDictData', 'district'),
            'street',
            'house_nr',
            'longtitude_2000',
            'lattitude_2000',
            'k_organization' => array('insertDictData', 'organization'),
            'close_date',//=>array('dateParser'),
            'k_source' => array('insertDictData', 'source')
        );

        if (preg_match('/_closed/', $fileName)) {
            $closed = true;
            $this->log('closed');
            $headerMapper[] = 'date';
        }
        $this->log('opened');

        $headers = array();
        $i = 0;
        foreach ($headerMapper as $key => $val) {
            if (is_array($val)) {
                $headers[$i] = $key;
            } else {
                $headers[$i] = $val;
            }
            $i++;
        }
        $header = NULL;
        $data = array();
        $res = array(
            'update' => 0,
            'insert' => 0
        );
        if (($handle = fopen($fileName, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                if (!$header) {
                    $header = $row;
                } else {
                    $tmp = array_combine($headers, $row);
                    $row =  $this->_prepareNotificationData($tmp, $headerMapper);
                    if ($closed) {
                        $u = $this->_updateNotification($row);
                        $this->log($row['number']. ' update result ' . $u);
                        $res['update']++;
                    } else {
                        $id = $this->_insertNotification($row);
                        $this->log($row['number'] . ' insert result ' . $id);
                        $res['insert']++;
                    }
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }
        $this->log(' //----------------------------------------------------------------');
        $this->log(' Dla pliku ' . $fileName . ' zaimportowano dane ' . $res['insert'] . ' updated ' . $res['update']);
        $this->log(' //----------------------------------------------------------------');
        return $data;
    }

    /**
     * @param $row
     * @param $header_mapper
     * @return array
     */
    protected function _prepareNotificationData($row, $header_mapper)
    {
        $rowToDb = array();
        foreach ($row as $key => $val) {
            if (array_key_exists($key, $header_mapper)) {
                $rowToDb[$key] = Medoo_Model::factory($header_mapper[$key][1])->insertIfNotExist($val);
            } else {
                $rowToDb[$key] = is_null($val) ? 'null' : $val;
            }
        }
        return $rowToDb;
    }

    /**
     * @param $row
     * @return bool
     */
    protected function _updateNotification($row)
    {
        try {
            $startDate = new DateTime($row['date_of_acceptance']);
            $closeDate = new DateTime($row['close_date']);

            $sinceStart = $startDate->diff($closeDate);
            $minutes = 'null';
            if ($sinceStart) {
                $minutes = $sinceStart->days * 24 * 60;
                $minutes += $sinceStart->h * 60;
                $minutes += $sinceStart->i;
            }

            $model = new p19115_Model_Notification();
            $model->update(array(
                'close_date' => $row['close_date'],
                'notification_time' => $minutes
            ), array(
                'number' => $row['number']
            ));
            return true;
        } catch (Execption $e) {
            $this->log('error on update' . $e->getMessage());
            return false;
        }
    }

    /**
     * @param $row
     * @return bool
     */
    protected function _insertNotification($row)
    {
        try {
            $model = new p19115_Model_Notification();
            $model->insert($row);
            return true;
        } catch (Execption $e) {
            $this->log('error on insert' . $e->getMessage());
            return false;
        }
    }
}