<?php
$ds = DIRECTORY_SEPARATOR;
$this->bootstrap = new Zend_Application(
                       'testing',
                       APPLICATION_PATH . "{$ds}configs/application.ini"
                   );
$this->bootstrap();

require_once dirname(__FILE__) . "{$ds}..{$ds}test_helper.php";
