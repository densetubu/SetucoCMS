<?php
/* 
 * tagテーブルのフィクスチャークラス
 */

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
        return array('page_id' => Fixture_Page::TAG_ID, 'tag_id' => Fixture_Tag::TEST_ID);
    }
    
    public function getDataOfSetuco()
    {
        return array('page_id' => Fixture_Page::TAG_ID, 'tag_id' => Fixture_Tag::SETUCO_ID);
    }
}

