<?php
/**
 * Model Mail
 *
 * @category   Mail_Model
 * @package    Mail_Model
 * @author     Michał Szkodziński
 * @version    $Id: $
 */
class Mail_Model_Imap_Mailbox_Horde extends Mail_Model_Imap_Mailbox_Abstract
{
    public function getMessagesUidSinceDate($date)
    {
        // Pobranie UID wiadomości
        $query = new Horde_Imap_Client_Search_Query();
        $query->dateSearch(
            new Horde_Date($date),
            Horde_Imap_Client_Search_Query::DATE_SINCE
        );
        $results = $this->_connection->getSocket()->search($this->_name, $query);

        return $results['match']->ids;
    }

    public function getMessagesUidOnDate($date)
    {
        // Pobranie UID wiadomości
        $query = new Horde_Imap_Client_Search_Query();
        $query->dateSearch(
            new Horde_Date($date),
            Horde_Imap_Client_Search_Query::DATE_ON
        );
        $results = $this->_connection->getSocket()->search($this->_name, $query);

        return $results['match']->ids;
    }

    public function getMessagesByUid($uids, $getStructure = true)
    {
        // Pobranie szczegółów wiadomości
        $query = new Horde_Imap_Client_Fetch_Query();
        if ($getStructure) {
            $query->structure();
        }
        $query->envelope();
        $query->flags();
        $query->size();

        $messageList = new Mail_Model_Imap_Message_List();
        foreach ($this->getHordeMessageByUid($uids, $query) as $m) {
            $message = $this->_makeMessageFromHordeMessage($m);
            // Pomijamy duże maile tekstowe, bez załączników
            if ($m->getSize() > 1 * 1024 * 1024 && !$message->hasAttachments()) {
                continue;
            }
            $messageList[] = $message;
        }
        return $messageList;
    }

    protected function _makeMessageFromHordeMessage($hordeMessage)
    {
        $envelope = $hordeMessage->getEnvelope();

        $message = new Mail_Model_Imap_Message_Horde($this);
        $message->setHordeMessage($hordeMessage)
            ->setUuid(sha1(
                $hordeMessage->getEnvelope()->message_id
                    ? $hordeMessage->getEnvelope()->message_id
                    : $hordeMessage->getEnvelope()->date->format('Y-m-d H:i:s') . $hordeMessage->getSize()
            ));

        foreach (array('from', 'to', 'cc', 'bcc', 'reply_to') as $field) {
            if (is_object($envelope->$field) && count($envelope->$field->raw_addresses)) {
                $addresses = array();
                foreach ($envelope->$field->raw_addresses as $address) {
                    $a = $address->bare_address;
                    if (substr($a, 0, 2) == '=?') {
                        $a = substr($a, 0, strpos($a, '?=') + 1);
                    }
                    $addresses[] = trim(mb_decode_mimeheader($a), ' \'');
                }
                $message->setParam($field, $addresses);
            }
        }

        $message->setParams(array(
            'mailbox' => $this->getName(),
            'subject' => $envelope->subject,
            'message_id' => $envelope->message_id,
            'in_reply_to' => $envelope->in_reply_to,
            'flags' => $hordeMessage->getFlags(),
            'size' => $hordeMessage->getSize(),
            'uid' => $hordeMessage->getUid(),
            'date' => $envelope->date->setTimezone(new DateTimeZone('Europe/Warsaw'))->format('Y-m-d H:i:s')
        ));

        return $message;
    }

    public function getHordeMessageByUid($uid, $query)
    {
        if (!is_array($uid)) {
            $uid = array($uid);
        }
        return $this->getConnection()->getSocket()->fetch($this->getName(), $query, array(
            'ids' => new Horde_Imap_Client_Ids($uid)
        ));
    }

    public function getOneHordeMessageByUid($uid, $query)
    {
        $list = $this->getHordeMessageByUid($uid, $query);
        return $list->first();
    }
}