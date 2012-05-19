<?php

define('ROOT_DIR', substr(preg_replace("/tests.*$/", '', __DIR__), 0, -1));
define('LIB_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR);
define('APPLICATION_PATH', ROOT_DIR . DIRECTORY_SEPARATOR . 'application');
define('TEST_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR);
define('BOOT_STRAP_FINSHED', true);


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIB_DIR),
    realpath(TEST_DIR . 'library' . DIRECTORY_SEPARATOR ),
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


