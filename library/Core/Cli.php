<?php
class Core_Cli
{
    /**
     * @param $applicationName
     * @param $scriptName
     * @param $data
     * @return mixed
     */
    final function run($applicationName, $scriptName, $data)
    {
        $className = $applicationName . '_Script_' . $scriptName;
        $script = new $className;
        return $script->run($data);
    }
}