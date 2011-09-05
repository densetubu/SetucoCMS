<?php
$this->bootstrap = new Zend_Application(
                       APPLICATION_ENV,
                       APPLICATION_PATH . '/configs/application.ini'
                   );
$this->bootstrap();

require_once dirname(__FILE__) . '/../test_helper.php';

