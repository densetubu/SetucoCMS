<?php


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

//Define SetucoCMS version
defined('APPLICATION_VERSION') || define('APPLICATION_VERSION', '0.1.0');    


// Define path to root directory
defined('ROOT_DIR')
    || define('ROOT_DIR', realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'));


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
$appIni = APPLICATION_PATH . '/configs/application.ini';
if (!file_exists($appIni)) {
    $appIni = APPLICATION_PATH . '/configs/application-sample.ini';

    //設定ファイルがなければインストーラへ飛ばす
    require('Zend/Controller/Router/Rewrite.php');
    require('Zend/Controller/Request/Http.php');
    $router = new Zend_Controller_Router_Rewrite();
    $req = $router->route(new Zend_Controller_Request_Http());

    $baseUrl = $req->getBaseUrl();
    $controller = $req->getControllerName();
    if ($controller != 'install') {
        header("Location: {$baseUrl}/install");
        return true;
    }
}
$application = new Zend_Application(
    APPLICATION_ENV,
    $appIni
);
$application->bootstrap()
            ->run();
