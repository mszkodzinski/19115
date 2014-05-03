<?php
class Api_Engine
{
    /**
     * @param $name
     * @param $data
     * @return mixed
     */
    final function run($name, $data)
    {
        $name = explode('_', $name);
        $className = array_shift($name) . '_Api_' . array_shift($name);
        $api = new $className;
        return $api->action(implode('_', $name), $data);
    }

    /**
     * @param $name
     * @param $data
     * @return string
     */
    public function action($name, $data)
    {
        $r = new Api_Responce();
        if (!method_exists($this, $name)) {
            $r->code = Api_Responce::CODE_ERROR;
            $r->status = false;
            return $r->serialize();
        }
        $r = $this->$name($data);
        return $r->serialize();
    }

    /**
     * @param $data
     * @return Api_Responce
     */
    public function test($data)
    {
        $r = new Api_Responce();
        $r->data = $data;
        return $r;
    }
}