<?php

/**
 *
 * @author suzuki-mar
 */

//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

class Common_DbTable_TemplateTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{

    public function setup()
    {
        parent::setup();
        
        $this->_dao = new Common_Model_DbTable_Template($this->getAdapter());
    }

    public function test_findFileNamesByAccountId_指定したアカウントIDのファイル名一覧を取得する()
    {
        $this->assertEquals(array('1'), $this->_dao->findFileNamesByAccountId(Fixture_Account::ADMIN_ID));
        $this->assertEquals(array('2'), $this->_dao->findFileNamesByAccountId(Fixture_Account::GENERAL_ID));
    }

    public function test_findNextAutoIncrementNumber_AutoIncrement_次のAutoIncrementの値を取得する()
    {
        $this->assertSame(3, $this->_dao->findNextAutoIncrementNumber());
    }

}



