<?php
/**
 * Created by PhpStorm.
 * User: mszkodzinski
 * Date: 14.03.14
 * Time: 22:29
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);


include_once('../config/config.php');
include_once('../application/autoloader.php');

$accountData = array(
    'username' => $config['mail']['login'],
    'password' => $config['mail']['pass'],
    'server' => $config['mail']['host'],
    'port' => $config['mail']['port'],
    'secure' => 'ssl',
);

$date = '2013-03-07';
$mailboxes = array('INBOX', '*Sent*', '*Wysłane*');

$connection = new Imap_Connection_Horde($accountData);
if (!$connection->isCorrect()) {
    echo "Failed";
    return false;
}

$db = Medoo_Medoo::getInstance();

// Pobierz wszystkie od daty... bez treści
foreach ($connection->getMailboxList($mailboxes) as $mailbox) {
    $uids = $mailbox->getMessagesUidSinceDate($date);

    foreach ($mailbox->getMessagesByUid($uids, true) as $message) {
        $model = $message->getModel();

        foreach ($message->getAttachments() as $a) {
            $am = $a->getModel();
            $file_name = substr($model['date'], 0, 10);

            switch (trim($model['subject'])) {
                case "Lista zamkniętych zgłoszeń z systemu CKM":
                    $file_name .= "_closed";
                    break;
                case "Lista zgłoszeń z systemu CKM":
                    $file_name .= "_open";
                    break;
            }

            $file_name .= ".csv";
            //echo $file_name."<br/>";

            $a->saveContent('../data/file/' . $file_name);
            //$db->insertFile($file_name);
            $db->insert("importer", [
                "file" => $file_name,
                "import_date" => "CURRENT_TIMESTAMP",
                "status" => 0
            ]);

        }

    }
}
