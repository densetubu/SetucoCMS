<?php
/* 
 * accountテーブルのフィクスチャークラス
 */

class Fixture_Account extends Setuco_Test_Fixture_Abstract
{
    const ADMIN_ID = 1;
    const GENERAL_ID = 2;
    const TARGET_ID = 3;

    public function getColumns()
    {
        return array('id', 'login_id', 'nickname', 'password');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        $password = Setuco_Util_String::convertHash2Password('password');

        return array(
              'login_id'        => 'setuco',
              'nickname'        => 'setuco',
              'password'        => $password,
            );
    }

    public function getDataOfAdmin()
    {
        return array('id' => self::ADMIN_ID, 'login_id' => 'admin', 'nickname' => 'setuco');
    }
    
    public function getDataOfGeneral()
    {
        return array('id' => self::GENERAL_ID, 'login_id' => 'user', 'nickname' => 'setuo');
    }

    public function getDataOfTarget()
    {
        return array('id' => self::TARGET_ID, 'login_id' => 'search', 'nickname' => '検索する人');
    }
}

