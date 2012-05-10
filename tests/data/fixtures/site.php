<?php
/* 
 * siteテーブルのフィクスチャークラス
 */

class Fixture_Site extends Setuco_Test_Fixture_Abstract
{
    public function getColumns()
    {
        return array('id', 'name', 'url', 'comment', 'keyword', 'open_date');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        return array(
              'name'            => 'SetucoCMS',
              'url'             => 'http://setucocms.localdomain',
              'comment'         => 'setucoCMSの開発サイト',
              'keyword'         => '公式,開発',
              'open_date'       => '2011-03-18 19:02:02',
            );
    }

    public function getDataOfFirst()
    {
        return array('id' => 1);
    }
    
}

