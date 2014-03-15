<?php
/**
 * Model Mail
 *
 * @category   Mail_Model
 * @package    Mail_Model
 * @author     Michał Szkodziński
 * @version    $Id: $
 */
abstract class Imap_Mailbox_Abstract
{
    protected $_connection;
    protected $_name;

    public function __construct(Imap_Connection_Abstract $connection, $name)
    {
        $this->_connection = $connection;
        $this->_name = $name;

        $this->init();
    }

    public function init()
    {
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getConnection()
    {
        return $this->_connection;
    }

    abstract public function getMessagesUidSinceDate($date);
}