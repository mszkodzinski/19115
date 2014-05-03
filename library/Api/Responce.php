<?php
class Api_Responce
{
    const CODE_OK = 200;
    const CODE_ERROR = 500;

    public $result = true;
    public $code = self::CODE_OK;
    public $data = array();
    public $error = array();

    /**
     * @return string
     */
    public function serialize()
    {
        return json_encode(array(
            'status' => $this->result,
            'code' => $this->code,
            'data' => $this->data,
            'error' => $this->error
        ));
    }
}