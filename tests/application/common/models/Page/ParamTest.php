<?php

/**
 *
 * @author suzuki-mar
 */
//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

class ParamTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{

    public function setup()
    {
        parent::setup();
        
        $keyword = '検索して';
        $tagIds = array(1, 2);
        $pageNumber = 1;
        $limit = 10;
        $targetColumns = array('title', 'contents', 'outline', 'tag');
        $refinements = array();
        $sortColumn = 'create_date';
        $order      = 'desc';
        $searchOperator = 'AND';

        $this->_params = new Common_Model_Page_Param($keyword, $tagIds, $pageNumber, $limit, $targetColumns, $refinements, $sortColumn, $order, $searchOperator);
    }

    public function testIsSettingSearchCondition_検索条件を指定してあるか_キーワードが指定してある場合はTRUE()
    {
        $this->assertTrue($this->_params->isSettingSearchCondition());
    }

    public function testIsSettingSearchCondition_オプションを指定している場合はTrue()
    {
        $this->_params->setDaoParams(array('keyword' => '', 'tagIds' => array(), 'refinements' => array('account_id' => 12)));
        $this->assertTrue($this->_params->isSettingSearchCondition());
    }

    public function testIsSettingSearchCondition_タグIDを指定している場合はTrue()
    {
        $this->_params->setDaoParams(array('keyword' => '', 'refinements' => array('account_id' => 12)));
        $this->assertTrue($this->_params->isSettingSearchCondition());
    }

    public function testIsSettingSearchCondition_キーワードとオプションを指定してない場合はFalse()
    {
        $this->_params->setDaoParams(array('keyword' => '', 'tagIds' => array()));
        $this->assertFalse($this->_params->isSettingSearchCondition());
    }
}



