<?php

define('ROOT_DIR', '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR);
define('APPLICATION_PATH', ROOT_DIR . DIRECTORY_SEPARATOR . 'application');


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIB_DIR),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    'testing',
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();


