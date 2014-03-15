<?php
class Api_Responce
{
    public $result = true;
    public $code = 200;
    public $data = array();
    public $error = array();

    public function serialize()
    {
        return json_encode(array(
            'result' => $this->result,
            'code' => $this->code,
            'data' => $this->data,
            'error' => $this->error
        ));
    }
}