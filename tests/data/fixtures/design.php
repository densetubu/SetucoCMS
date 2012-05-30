<?php
/* 
 * designテーブルのフィクスチャークラス
 */

class Fixture_Design extends Setuco_Test_Fixture_Abstract
{
    public function getColumns()
    {
        return array('id', 'design_name');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        return array(
              'design_name'        => 'default',
            );
    }

    public function getDataOfFirst()
    {
        return array('id' => 1);
    }
    
}

