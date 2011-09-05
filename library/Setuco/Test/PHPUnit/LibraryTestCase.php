<?php

abstract class Setuco_Test_PHPUnit_LibraryTestCase extends PHPUnit_Framework_TestCase
{
    public $bootstrap = APPLICATION_TEST_BOOTSTRAP_PATH;

    protected function setUp()
    {
        $this->bootstrap();
    }

    final public function bootstrap()
    {
        if (null !== $this->bootstrap) {
            if ($this->bootstrap instanceof Zend_Application) {
                $this->bootstrap->bootstrap();
            } elseif (is_callable($this->bootstrap)) {
                call_user_func($this->bootstrap);
            } elseif (is_string($this->bootstrap)) {
                require_once 'Zend/Loader.php';
                if (Zend_Loader::isReadable($this->bootstrap)) {
                    include $this->bootstrap;
                }
            }
        }
    }

}


