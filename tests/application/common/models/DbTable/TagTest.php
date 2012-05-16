<?php

/**
 *
 * @author suzuki-mar
 */

//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}


class TagTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{

    public function setup()
    {
        parent::setup();
        
        $this->_dao = new Common_Model_DbTable_Tag($this->getAdapter());
    }

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



