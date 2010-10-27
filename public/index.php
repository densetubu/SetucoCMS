<?php


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

//Define SetucoCMS version
defined('APPLICATION_VERSION') || define('APPLICATION_VERSION', '0.1.0');    
    
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/**
 * デバッグ用関数
 * 
 * @param mixed $var デバッグしたい変数
 * @param string $label デバッグ表示の際のラベル
 */
function d($var, $label = null) {
    Zend_Debug::dump($var, $label);
}

/** Zend_Application */
require_once 'Zend/Application.php';



// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();