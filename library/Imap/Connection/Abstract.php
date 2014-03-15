<?php
/**
 * Model Mail
 *
 * @category   Mail_Model
 * @package    Mail_Model
 * @author     Michał Szkodziński
 * @version    $Id: $
 */
abstract class Imap_Connection_Abstract
{
    protected $_params;

    protected $_socket;

    public function __construct($params = array())
    {
        if ($params instanceof Account) {
            $this->_params = $params->toArray();
            $this->_params['password'] = $params->decrypt($this->_params['password']);
        } else {
            $this->_params = $params;
        }
        $this->init();
    }

    public function init()
    {
        $this->setSocket();
    }

    abstract public function setSocket();
    abstract public function setSocketParams();

    public function setParams($params)
    {
        if ($params instanceof Account) {
            $this->_params = $params->toArray();
            $this->_params['password'] = $params->decrypt($this->_params['password']);
        } else {
            $this->_params = $params;
        }
        $this->setSocketParams();
        return $this;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function getSocket()
    {
        return $this->_socket;
    }

    abstract public function isCorrect();

    /**
     * @return Imap_Mailbox_List
     */
    abstract public function getMailboxList();
}