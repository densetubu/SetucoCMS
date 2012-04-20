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


    public function testloadTagIdsByKeyword_キーワードからタグIDを取得する()
    {
        $expected = $this->_createExpected->createTagIdsByKeyword('test');
        $this->assertEquals($expected, $this->_dao->loadTagIdsByKeyword('test'));
    }

    public function testloadTagIdsByKeyword_キーワードからタグIDを取得する_複数キーワードに対応している()
    {
        $expected = $this->_createExpected->createTagIdsByKeyword('test setuco');
        $actual = $this->_dao->loadTagIdsByKeyword('test setuco');

        sort($expected);
        sort($actual);

        $this->assertEquals($expected, $actual);
    }

}



