<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

define('APPLICATION_TEST_BOOTSTRAP_PATH', realpath(dirname(__FILE__) . '/application/bootstrap.php'));
define('LIBRARY_TEST_BOOTSTRAP_PATH', realpath(dirname(__FILE__) . '/library/bootstrap.php'));

set_include_path(
     APPLICATION_PATH . '/../library' . PATH_SEPARATOR .
     APPLICATION_PATH . '/controllers' . PATH_SEPARATOR .
     realpath(dirname(__FILE__) . '/testrunner') . PATH_SEPARATOR .
     get_include_path()
);

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(TRUE);
