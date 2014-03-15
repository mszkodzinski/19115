<?php
class Api_Engine
{
    public function action($name, $data)
    {
        $r = new Api_Responce();
        switch ($name) {
            case 'test':
                $r = $this->test($data);
                break;
            case 'getData':
                $r = $this->getData($data);
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

    public function getData($params)
    {
        foreach (array('filter', 'groupby', 'sortby', 'order') as $key) {
            if (!isset($params[$key])) {
                $params[$key] = null;
            }
        }
        $reader = new Reader_Data();
        $result = $reader->getData($params);

        $r = new Api_Responce();
        if (!$result) {
            $r->status = false;
            $r->code = 500;
        } else {
            $r->data = $result;
        }
        return $r;
    }
}