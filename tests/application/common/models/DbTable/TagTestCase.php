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
        parent::setup();
        
        $this->_dao = new Common_Model_DbTable_Tag($this->getAdapter());
    }

//    private function _createSearchResultExpectedData($id)
//    {
//        $expectd = $this->_createExpected->createPageDataByPageId($id);
//
//        $expectd['category_name'] = $this->_createExpected->createCategoryNameByCategoryId($expectd['category_id']);
//        $expectd['nickname'] = $this->_createExpected->createNickNameByAccountId($expectd['account_id']);
//
//        return $expectd;
//    }


    public function testFirst()
    {
        $this->assertTrue(true);
    }



}



