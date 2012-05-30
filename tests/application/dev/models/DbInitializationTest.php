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


        $this->_categoryDao = new Common_Model_DbTable_Category($this->getAdapter());
        $this->_pageDao = new Common_Model_DbTable_Page($this->getAdapter());
    }

    public function test_findAllTableNames_テーブル名を取得する()
    {
        $expecteds = array('account', 'category', 'media');

        foreach ($expecteds as $expected) {
            $this->assertArrayHasElement($expected, $this->_service->findAllTableNames());
        }

    }




//    public function test_dropAllTables_全てのテーブルを削除する()
//    {
//        $this->_service->dropAllTables();
//
//
//
//    }


//    public function test_initializeDb_DBを初期化する()
//    {
//        $this->_service->truncateAllTables();
//        $this->_service->initializeDb();
//
//        $this->assertNotSame(0, $this->_categoryDao->fetchAll()->count());
//        $this->assertNotSame(0, $this->_pageDao->fetchAll()->count());
//    }


    public function test_truncateAllTables_DBを空にできたか()
    {
        $this->_service->truncateAllTables();
        $this->assertSame(0, $this->_categoryDao->fetchAll()->count());
        $this->assertSame(0, $this->_pageDao->fetchAll()->count());
    }

    public function test_loadAllFixtureDatas_フィクスチャーデータを取得する()
    {
        $this->_service->truncateAllTables();
        $this->_service->loadAllFixtureDatas();

        $this->assertNotSame(0, $this->_categoryDao->fetchAll()->count());
        $this->assertNotSame(0, $this->_pageDao->fetchAll()->count());
    }
    

}

