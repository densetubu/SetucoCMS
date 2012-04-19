<?php
/* 
 * tagテーブルのフィクスチャークラス
 */

//page_id,tag_id
//5,1
//5,2

class Fixture_Page_Tag extends Setuco_Test_Fixture_Abstract
{
    public function getColumns()
    {
        return array('page_id', 'tag_id');
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
        return array('page_id' => 5, 'tag_id' => 1);
    }
    
    public function getDataOfSetuco()
    {
        return array('page_id' => 5, 'tag_id' => 2);
    }
}

