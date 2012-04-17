<?php

/**
 *
 * @author suzuki-mar
 */
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';


class PageTestCase extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->_dao = new Common_Model_DbTable_Page();
    }

    

}



