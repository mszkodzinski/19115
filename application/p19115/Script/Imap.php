<?php

class p19115_Script_Imap extends Core_CliScript
{
    public function run($data)
    {
        $service = new p19115_Service_Import();
        $service->read();
    }
}
