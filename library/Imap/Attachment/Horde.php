<?php
/**
 * Model Mail
 *
 * @category   Mail_Model
 * @package    Mail_Model
 * @author     Michał Szkodziński
 * @version    $Id: $
 */
class Imap_Attachment_Horde extends Imap_Attachment_Abstract
{
    /**
     * @var Imap_Message_Horde
     */
    protected $_message;

    protected $_content;

    public function setMessage(Imap_Message_Horde $message)
    {
        $this->_message = $message;
        return $this;
    }

    public function getContent()
    {
        if (null === $this->_content) {
            $this->_content = $this->_message->getPartDecoded($this->getMimeId());
        }
        return $this->_content;
    }

    public function saveContent($fileName)
    {
        file_put_contents($fileName, $this->getContent());
    }

    public function getModel()
    {
        return array(
            'name' => $this->getName(),
            'size' => intval($this->getSize()),
            'mime_type' => $this->getMimeType(),
            'mime_id' => $this->getMimeId()
        );
    }
}