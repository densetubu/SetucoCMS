<?php

//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

class Dev_DbTable_DbInitializationTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{
    public function setup()
    {
        parent::setup();
        $this->_service = new Dev_Model_DbInitialization($this->getAdapter());

    }

    public function test_truncateAllTables_DBを空にできたか()
    {
        $categoryDao = new Common_Model_DbTable_Category($this->getAdapter());
        $pageDao = new Common_Model_DbTable_Page($this->getAdapter());

        $this->_service->truncateAllTables();

        $this->assertSame(0, $categoryDao->fetchAll()->count());
        $this->assertSame(0, $pageDao->fetchAll()->count());
    }

    public function test_loadAllFixtureDatas_フィクスチャーデータを取得する()
    {
        $this->_service->truncateAllTables();

        $categoryDao = new Common_Model_DbTable_Category($this->getAdapter());
        $pageDao = new Common_Model_DbTable_Page($this->getAdapter());

        $this->_service->loadAllFixtureDatas();

        $this->assertNotSame(0, $categoryDao->fetchAll()->count());
        $this->assertNotSame(0, $pageDao->fetchAll()->count());
    }
    

}

