<?php
$ds = DIRECTORY_SEPARATOR;
$this->bootstrap = new Zend_Application(
                       APPLICATION_ENV,
                       APPLICATION_PATH . "{$ds}configs{$ds}application.ini"
                   );
$this->bootstrap();

require_once dirname(__FILE__) . "{$ds}..{$ds}test_helper.php";

