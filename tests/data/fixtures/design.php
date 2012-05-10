<?php
/* 
 * free-spaceテーブルのフィクスチャークラス
 */

class Fixture_Free_Space extends Setuco_Test_Fixture_Abstract
{
    public function getColumns()
    {
        return array('id', 'content');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        return array(
              'content'        => 'フリースペース',
            );
    }

    public function getDataOfFirst()
    {
        return array('id' => 1);
    }
    
}

