<?php
/*
 * ambitionテーブルのフィクスチャークラス
 */

class Fixture_Ambition extends Setuco_Test_Fixture_Abstract
{
    public function getColumns()
    {
        return array('id', 'ambition');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        return array(
              'ambition'        => 'SetucoCMSを開発する',
            );
    }

    public function getDataOfFirst()
    {
        return array('id' => 1);
    }

}

