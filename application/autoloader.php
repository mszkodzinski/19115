<?php
setlocale(LC_CTYPE, "pl.UTF8");

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

define('LIB_PATH', '../library/');
define('APP_PATH', '../application/');

if(!ini_get('date.timezone')) {
    date_default_timezone_set('Europe/Warsaw');
}

$lib = array(

);

/**
 * Autoloader
 *
 * @param string $class nazwa klasy
 */
function __autoload($class)
{
    if (class_exists($class, false) || interface_exists($class, false)) {
        return;
    }
    $name = explode('_', $class);
    switch ($name[0]) {
        case 'Horde':
        case 'Core':
        case 'Csv':
        case 'Api':
        case 'DB':
        case 'Medoo':
        case 'Imap':
            $path = LIB_PATH;
            $class = str_replace('_', '/', $class);
            break;
        default:
            if (isset($name[1])) {
                $name[1] = strtolower($name[1]);
            }
            $path = APP_PATH;
            $class = str_replace('_', '/', $class);
    }
    if (!file_exists($path . $class . '.php')) {
        echo $class . ' in file ' . $path . $class . '.php';
        die;
    }
    require $path . $class . '.php';
}
spl_autoload_register('__autoload');

// Ścieżki do bibliotek i aplikacji
set_include_path(
    LIB_PATH . PATH_SEPARATOR .
    APP_PATH
);