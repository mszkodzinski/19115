<?php
/**
 * Model Mail
 *
 * @category   Mail_Model
 * @package    Mail_Model
 * @author     Michał Szkodziński
 * @version    $Id: $
 */
class Imap_Connection_Horde extends Imap_Connection_Abstract
{
    protected static $_keyMap = array(
        'username' => 'username',
        'password' => 'password',
        'server' => 'hostspec',
        'port' => 'port',
        'secure' => 'secure',
    );


    public function setSocket()
    {
        $this->_socket = new Horde_Imap_Client_Socket($this->getSocketParams());
    }

    public function setSocketParams()
    {
        foreach ($this->getSocketParams() as $key => $value) {
            $this->_socket->setParam($key, $value);
        }
        return $this;
    }

    public function getSocketParams()
    {
        $params = array();
        foreach (static::$_keyMap as $key => $keySocket) {
            $params[$keySocket] = $this->_params[$key];
        }
        return $params;
    }

    public function isCorrect()
    {
        try {
            $this->_socket->login();
        } catch (Horde_Imap_Client_Exception $e) {
            return false;
        }
        return true;
    }

    public function getMailboxList($pattern = null)
    {
        if (null === $pattern) {
            $pattern = '*';
        }
        $boxes = new Imap_Mailbox_List();
        foreach ($this->_socket->listMailboxes($pattern, Horde_Imap_Client::MBOX_ALL) as $box) {
            if (isset($box['mailbox']) && $box['mailbox']->utf8) {
                $boxes[] = new Imap_Mailbox_Horde($this, $box['mailbox']->utf8);
            }
        }
        return $boxes;
    }
}