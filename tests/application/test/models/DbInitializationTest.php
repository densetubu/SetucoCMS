<?php

//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

class Test_DbTable_DbInitializationTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{
    private function _initDb()
    {
        if ($this->_service->emptyDb()) {
            $this->_service->initializeDb();
        }
    }

    private function _dropDb()
    {
        if ($this->_service->existsTable()) {
            $this->_service->dropAllTables();
        }
    }


    public function setup()
    {
        $this->_service = new Test_Model_DbInitialization('test');
        $this->_categoryDao = new Common_Model_DbTable_Category($this->getAdapter());
        $this->_pageDao = new Common_Model_DbTable_Page($this->getAdapter());
        $this->_accountDao = new Common_Model_DbTable_Account($this->getAdapter());
    }

    public function test_findAllTableNames_テーブル名を取得する()
    {
        $expecteds = array('account', 'category', 'media');

        foreach ($expecteds as $expected) {
            $this->assertArrayHasElement($expected, $this->_service->findAllTableNames());
        }
    }

    public function test_dropAllTables_全てのテーブルを削除する()
    {
        $this->_service->dropAllTables();
        $this->assertEmpty($this->_service->findAllTableNames());
        $this->_service->initializeDb();
    }

    


    public function test_dropAllTables_すでにテーブルがなかったら例外が発生する()
    {
        $this->_initDb();
        $this->_service->dropAllTables();

        $this->setExpectedException('RuntimeException');
        $this->_service->dropAllTables();
    }


    public function test_emptyDb_DBにテーブルがなかったらtrueを返す()
    {
        $this->_dropDb();
        $this->assertTrue($this->_service->emptyDb());
    }

    public function test_emptyDb_DBにテーブルが存在したらfalseを返す()
    {
        $this->_dropDb();
        $this->_service->initializeDb();
        $this->assertFalse($this->_service->emptyDb());
    }

    public function test_existsDb_DBにテーブルが存在したらtrueを返す()
    {
        $this->_dropDb();
        $this->_service->initializeDb();
        $this->assertTrue($this->_service->existsTable());
    }

    public function test_existsDb_DBにテーブルが存在しなかったらfalseを返す()
    {
        $this->_initDb();
        $this->_service->dropAllTables();
        $this->assertFalse($this->_service->existsTable());
    }

    public function test_initializeDb_DBを初期化する()
    {
        $this->_dropDb();
        $this->_service->initializeDb();
        $this->assertTrue($this->_service->existsTable());
    }

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

