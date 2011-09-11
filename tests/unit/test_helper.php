<?php
$ds = DIRECTORY_SEPARATOR;
// Define path to application directory
defined("APPLICATION_PATH")
    || define("APPLICATION_PATH", realpath(dirname(__FILE__) . "${ds}..${ds}..${ds}application"));

// Define application environment
defined("APPLICATION_ENV")
    || define("APPLICATION_ENV", (getenv("APPLICATION_ENV") ? getenv("APPLICATION_ENV") : "testing"));

define("APPLICATION_TEST_BOOTSTRAP_PATH", realpath(dirname(__FILE__) . "${ds}application${ds}bootstrap.php"));
define("LIBRARY_TEST_BOOTSTRAP_PATH", realpath(dirname(__FILE__) . "${ds}library${ds}bootstrap.php"));

set_include_path(
     APPLICATION_PATH . "${ds}..${ds}library" . PATH_SEPARATOR .
     APPLICATION_PATH . "${ds}controllers" . PATH_SEPARATOR .
     realpath(dirname(__FILE__) . "${ds}testrunner") . PATH_SEPARATOR .
     get_include_path()
);

require_once "Zend${ds}Loader${ds}Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(TRUE);
