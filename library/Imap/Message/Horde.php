<?php
/**
 * Model Mail
 *
 * @category   Mail_Model
 * @package    Mail_Model
 * @author     Michał Szkodziński
 * @version    $Id: $
 */
class Imap_Message_Horde extends Imap_Message_Abstract
{
    protected static $_modelParam = array(
        'from', 'to', 'cc', 'bcc', 'reply_to', 'subject', 'uid', 'size', 'date', 'flags', 'message_id', 'mailbox'
    );

    protected $_hordeMessage = null;
    protected $_clearBody = null;
    protected $_textBody = null;
    protected $_htmlBody = null;

    public function setHordeMessage($hm)
    {
        $this->_hordeMessage = $hm;
        return $this;
    }

    public function getHordeMessage()
    {
        return $this->_hordeMessage;
    }

    public function getClearBody()
    {
        if (null === $this->_clearBody) {
            $this->_clearBody = $this->_getClearBody();
        }
        return $this->_clearBody;
    }

    protected function _getClearBody()
    {
        $bodyPartId = null;
        $parts = $this->_hordeMessage->getStructure();

        $isHtml = true;
        $bodyPartId = $parts->findBody('html');
        if ($bodyPartId === null) {
            $bodyPartId = $parts->findBody('plain');
            $isHtml = false;
            if ($bodyPartId === null) {
                $bodyPartId = $parts->findBody();
                $isHtml = false;
            }
        }
        if (!$bodyPartId) {
            $parts = $this->_hordeMessage->getStructure();
            foreach ($parts->contentTypeMap() as $_mimeId => $mimeType) {
                $part = $parts->getPart($_mimeId);
                if ($part->getDisposition() !== 'attachment' && false === strpos(strtolower($mimeType), 'application/')) {
                    $bodyPartId = $_mimeId;
                }
            }
        }
        if (null === $bodyPartId) {
            return '';
        }

        return Utils::clearNonUtf8Characters(static::clearText($this->getPartDecoded($bodyPartId), $isHtml));
    }

    public function getTextBody()
    {
        if (null === $this->_textBody) {
            $this->_textBody = $this->_getTextBody();
        }
        return $this->_textBody;
    }

    protected function _getTextBody()
    {
        $bodyPartId = null;
        $parts = $this->_hordeMessage->getStructure();

        $bodyPartId = $parts->findBody('plain');
        if ($bodyPartId === null) {
            $bodyPartId = $parts->findBody();
        }
        if (!$bodyPartId) {
            $parts = $this->_hordeMessage->getStructure();
            foreach ($parts->contentTypeMap() as $_mimeId => $mimeType) {
                $part = $parts->getPart($_mimeId);
                if ($part->getDisposition() !== 'attachment' && false === strpos(strtolower($mimeType), 'application/')) {
                    $bodyPartId = $_mimeId;
                }
            }
        }
        if (null === $bodyPartId) {
            return '';
        }

        return Utils::clearNonUtf8Characters($this->getPartDecoded($bodyPartId));
    }

    public function getHtmlBody()
    {
        if (null === $this->_htmlBody) {
            $this->_htmlBody = $this->_getHtmlBody();
        }
        return $this->_htmlBody;
    }

    protected function _getHtmlBody()
    {
        $parts = $this->_hordeMessage->getStructure();

        $bodyPartId = $parts->findBody('html');
        if (!$bodyPartId) {
            return '';
        }

        return Utils::clearNonUtf8Characters($this->getPartDecoded($bodyPartId));
    }

    public function getPartDecoded($partId)
    {
        $query = new Horde_Imap_Client_Fetch_Query();
        $query->bodyPart($partId, array(
            'decode' => true,
            'peek' => true
        ));
        $message = $this->_mailbox->getOneHordeMessageByUid($this->_hordeMessage->getUid(), $query);

        $part = $this->_hordeMessage->getStructure()->getPart($partId);
        $text = $message->getBodyPart($partId);
        if (!$message->getBodyPartDecode($partId)) {
            $part->setContents($text);
            $text = $part->getContents();
        }
        if ($part->getCharset()) {
            try {
                $text = iconv($part->getCharset(), 'utf-8', $text);
            } catch (Exception $e) {
//                $text = utf8_encode($text);
            }
        }
        return $text;
    }

    public function getAttachments($mimeId = null)
    {
        $attachments = new Imap_Attachment_List();

        $parts = $this->_hordeMessage->getStructure();
        foreach ($parts->contentTypeMap() as $_mimeId => $mimeType) {
            if ($mimeId && $mimeId != $_mimeId) {
                continue;
            }
            $part = $parts->getPart($_mimeId);
            if ($part->getDisposition() !== 'attachment' && $part->getDisposition() !== 'inline') {
                continue;
            }
            $p = $part->getAllDispositionParameters();

            $attachment = new Imap_Attachment_Horde();
            $attachment->setMessage($this)
                ->setName(isset($p['filename']) ? $p['filename'] : 'noname')
                ->setSize(isset($p['size']) ? $p['size'] : 0)
                ->setMimeId($_mimeId)
                ->setMimeType($mimeType);

            $attachments[] = $attachment;
        }
        return $attachments;
    }

//    public function getParts()
//    {
//        $parts = $this->_hordeMessage->getStructure();
//        foreach ($parts->contentTypeMap() as $_mimeId => $mimeType) {
//            echo 'mimeId: ' . $_mimeId . "\r\n";
//            echo 'mimeType: ' . $mimeType . "\r\n";
//            $part = $parts->getPart($_mimeId);
//            echo 'disposition: ' . $part->getDisposition() . "\r\n";
//            if ($part->getDisposition() !== 'attachment') {
//                $p = $part->getAllDispositionParameters();
//                echo 'name: ' . (isset($p['filename']) ? $p['filename'] : 'noname') . "\r\n";
//                echo 'size: ' . (isset($p['size']) ? $p['size'] : 0) . "\r\n";
//            }
//            echo '----' . "\r\n";
//        }
//    }

    public function hasAttachments()
    {
        $parts = $this->_hordeMessage->getStructure();
        foreach ($parts->contentTypeMap() as $mimeId => $mimeType) {
            if ($parts->getPart($mimeId)->getDisposition() == 'attachment' || $parts->getPart($mimeId)->getDisposition() == 'inline') {
                return true;
            }
        }
        return false;
    }

    public function getModel()
    {
        $data = array();
        foreach (static::$_modelParam as $paramName) {
            if ($value = $this->getParam($paramName)) {
                if (is_array($value)) {
                    $data[$paramName] = Core_Utils::clearNonUtf8Characters(implode(',', $value));
                } else if (is_numeric($value)) {
                    $data[$paramName] = intval($value);
                } else {
                    $data[$paramName] = Core_Utils::clearNonUtf8Characters(strval($value));
                }
            }
        }
        foreach (array('from', 'to', 'reply_to', 'flags') as $fieldName) {
            if (!isset($data[$fieldName])) {
                continue;
            }
            $data[$fieldName] = $this->_cutString($data[$fieldName], 255);
        }
        foreach (array('cc', 'bcc', 'subject') as $fieldName) {
            if (!isset($data[$fieldName])) {
                continue;
            }
            $data[$fieldName] = $this->_cutString($data[$fieldName], 500);
        }
        $data['body_clear'] = $this->getClearBody();
        $data['body_text'] = $this->getTextBody();
        $data['body_html'] = $this->getHtmlBody();
        $data['uuid'] = $this->getUuid();
        $data['in_reply_to_message_id'] = $this->getParam('in_reply_to');
        if (!empty($data['in_reply_to_message_id'])) {
            $inReply = Message::factory()->getIds(array('message_id', $data['in_reply_to_message_id']));
            if (count($inReply)) {
                $data['in_reply_to_id'] = array_shift($inReply);
            }
        }
        $data['has_attachments'] = (int)$this->hasAttachments();

        return $data;
    }

    protected function _cutString($str, $limit)
    {
        if (mb_strlen($str) <= $limit) {
            return $str;
        }
        if (null !== ($pos = mb_strpos($str, ',', round(0.9 * $limit)))) {
            return mb_substr($str, 0, $pos);
        }
        return mb_substr($str, 0, $limit);
    }
}