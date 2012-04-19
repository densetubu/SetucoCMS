<?php
/* 
 * tagテーブルのフィクスチャークラス
 */

class Fixture_Tag extends Setuco_Test_Fixture_Abstract
{
    const TEST_ID = 1;
    const SETUCO_ID = 2;
    const SETUO_ID = 3;
    
    public function getColumns()
    {
        return array('id', 'name');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    protected function _getFixtureBase()
    {
        return array();
    }

    public function getDataOfTest()
    {
        return array('id' => self::TEST_ID, 'name' => 'test');
    }
    
    public function getDataOfSetuco()
    {
        return array('id' => self::SETUCO_ID, 'name' => 'setuco');
    }

    public function getDataOfSetuo()
    {
        return array('id' => self::SETUO_ID, 'name' => 'setuo');
    }
}

