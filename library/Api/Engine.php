<?php
class Api_Engine
{
    public function action($name, $data)
    {
        switch ($name) {
            case 'test':
                $r = $this->test($data);
                break;
        }
        return $r->serialize();
    }

    public function test($data)
    {
        $r = new Api_Responce();
        $r->data = $data;
        return $r;
    }

    public function getData($data)
    {

        $r = new Api_Responce();

        return $r;
    }
}