<?php
/**
 * Model Mail
 *
 * @category   Mail_Model
 * @package    Mail_Model
 * @author     Michał Szkodziński
 * @version    $Id: $
 */
abstract class Mail_Model_Imap_Message_Abstract
{
    const MESSAGE_SPLITTER = '-----Original Message-----';

    protected $_mailbox;
    protected $_params = array();
    protected $_uuid = null;

    public function __construct(Mail_Model_Imap_Mailbox_Abstract $mailbox)
    {
        $this->_mailbox = $mailbox;
        $this->init();
    }

    public function init()
    {
    }

    public function getParam($name, $defaultValue = null)
    {
        if (isset($this->_params[$name])) {
            return $this->_params[$name];
        }
        return $defaultValue;
    }

    public function getParams()
    {
        return $this->_params;
    }

    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    public function setParams($params, $removeOld = false)
    {
        $this->_params = $removeOld ? $params : array_merge($this->_params, $params);
        return $this;
    }

    public function getUuid()
    {
        return $this->_uuid;
    }

    public function setUuid($uuid)
    {
        $this->_uuid = $uuid;
        return $this;
    }

    public function getMailbox()
    {
        return $this->_mailbox;
    }

    abstract public function getTextBody();
    abstract public function getAttachments();
    abstract public function getModel();

    public static function clearText($text, $isHtml = false)
    {
        $c = new Core_Html2Text($text, false, array(
            'width' => 0
        ));
        if ($isHtml) {
            $text = $c->get_text();
        }
        $text = $c->text2text($text);

        $text = preg_replace(array(
            '/^((>(\s*))*(FROM:|From:|OD:|Od:|Wiadomość napisana przez|Wiadomosc napisana przez|W dniu ([[:print:]]*) napisa))/m',
            '/(^(' . static::MESSAGE_SPLITTER . ')(\R)+){2,}/im'
        ), array(
            static::MESSAGE_SPLITTER . "\n$1",
            static::MESSAGE_SPLITTER . "\n"
        ), $text);
        return $text;
    }
}