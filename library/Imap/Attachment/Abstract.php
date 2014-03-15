<?php
/**
 * Model Mail
 *
 * @category   Mail_Model
 * @package    Mail_Model
 * @author     Michał Szkodziński
 * @version    $Id: $
 */
abstract class Imap_Attachment_Abstract
{
    protected $_name;
    protected $_size;
    protected $_mimeType;
    protected $_mimeId;

    public function __construct()
    {
    }

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function setSize($size)
    {
        $this->_size = $size;
        return $this;
    }

    public function setMimeType($mimeType)
    {
        $this->_mimeType = $mimeType;
        return $this;
    }

    public function setMimeId($mimeId)
    {
        $this->_mimeId = $mimeId;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getSize()
    {
        return $this->_size;
    }

    public function getMimeType()
    {
        return $this->_mimeType;
    }

    public function getMimeId()
    {
        return $this->_mimeId;
    }

    abstract public function getContent();
    abstract public function getModel();
}