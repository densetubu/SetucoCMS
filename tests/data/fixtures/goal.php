<?php
/* 
 * goalテーブルのフィクスチャークラス
 */

class Fixture_Goal extends Setuco_Test_Fixture_Abstract
{
    public function getColumns()
    {
        return array('id', 'page_count', 'target_month');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        return array(
              'page_count'        => 1,
            );
    }

    public function getDataOfFirst()
    {
        return array('id' => 1, 'target_month' => "2012-03-01");
    }

    public function getDataOfSecond()
    {
        return array('id' => 2, 'target_month' => "2012-04-01");
    }
    
}

