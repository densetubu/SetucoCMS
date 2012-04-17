<?php

/**
 *
 * @author suzuki-mar
 */
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';


class PageTestCase extends Setuco_Test_PHPUnit_DatabaseTestCase
{


    public function setup()
    {
        parent::setUp();

        $this->_dao = new Common_Model_DbTable_Account($this->getAdapter());
    }

    public function test_first()
    {
        $this->assertDataSetsEqual($this->getDataSet('account'), $this->getConnection()->createDataSet(array('account')));
    }

}



