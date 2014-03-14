<?php
/**
 * Created by PhpStorm.
 * User: mszkodzinski
 * Date: 14.03.14
 * Time: 22:29
 */

$accountData = array(
    'username' => 'username',
    'password' => 'password',
    'server' => 'hostspec',
    'port' => 'port',
    'secure' => 'secure',
);
$date = '2014-01-01';
$mailboxes = array('INBOX', '*Sent*', '*Wysłane*');

$connection = new Mail_Model_Imap_Connection_Horde($accountData);
if (!$connection->isCorrect()) {
    return false;
}

// Pobierz wszystkie od daty... bez treści
foreach ($connection->getMailboxList($mailboxes) as $mailbox) {
    $uids = $mailbox->getMessagesUidSinceDate($date);
    foreach ($mailbox->getMessagesByUid($uids, false) as $message) {
        $model = $message->getModel();
        // Zrób coś z wiadomością...
    }
}

// Pobierz po uid z treścią
foreach ($connection->getMailboxList($mail->mailbox) as $mailbox) {
    foreach ($mailbox->getMessagesByUid($mail->uid, true) as $message) {
        $model = $message->getModel();

        foreach ($message->getAttachments() as $a) {
            $am = $a->getModel();
            // zrób coś z załącznikiem...
            $a->saveContent('/tmp/' . $am->name);
        }
    }
}