<?php
/*
 * page_mediaテーブルのフィクスチャークラス
 */

class Fixture_page_media extends Setuco_Test_Fixture_Abstract
{
    public function getColumns()
    {
        return array('page_id', 'media_id');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        return array(
              'page_id'   => Fixture_Page::TITLE_ID,
            );
    }

    public function getDataOfFirst()
    {
        return array('media_id' => Fixture_Media::ID_JPEG);
    }

    public function getDataOfSecond()
    {
        return array('media_id' => Fixture_Media::ID_PNG);
    }

}
