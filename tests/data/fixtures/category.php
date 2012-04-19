<?php
/* 
 * categoryテーブルのフィクスチャークラス
 */

class Fixture_Category extends Setuco_Test_Fixture_Abstract
{
    const ROOT_ID = -1;
    const TEST_ID = 1;
    
    public function getColumns()
    {
        return array('id', 'name', 'parent_id');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    protected function _getFixtureBase()
    {
        return array(
              'name'            => 'setuco',
              'parent_id'       =>  -1,
            );
    }

    public function getDataOfRoot()
    {
        return array('id' => self::ROOT_ID, 'name' => 'no_parent', 'parent_id' => null);
    }
    
    public function getDataOfTest()
    {
        return array('id' => self::TEST_ID, 'name' => 'test');
    }
}

