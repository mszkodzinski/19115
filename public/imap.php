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

print_r($accountData);

$date = '2014-03-14';
$mailboxes = array('INBOX', '*Sent*', '*Wysłane*');

$connection = new Imap_Connection_Horde($accountData);
if (!$connection->isCorrect()) {
    echo "Failed";
    return false;
}

// Pobierz wszystkie od daty... bez treści
foreach ($connection->getMailboxList($mailboxes) as $mailbox) {
    $uids = $mailbox->getMessagesUidSinceDate($date);
    foreach ($mailbox->getMessagesByUid($uids, true) as $message) {
        $model = $message->getModel();
echo $model['from'].'---';
echo $model['date'].'---';

        foreach ($message->getAttachments() as $a) {
            $am = $a->getModel();
            // zrób coś z załącznikiem...
            echo $am['name'];
            $a->saveContent('../tmp/' . $am['name']);
        }
        //2014-03-01_open.csv
        // Zrób coś z wiadomością...
    }
}

// Pobierz po uid z treścią
/*
foreach ($connection->getMailboxList($mail->mailbox) as $mailbox) {
    foreach ($mailbox->getMessagesByUid($mail->uid, true) as $message) {
        $model = $message->getModel();

        echo $model;

        foreach ($message->getAttachments() as $a) {
            $am = $a->getModel();
            // zrób coś z załącznikiem...
            $a->saveContent('/tmp/' . $am->name);
        }
    }
}
*/