<?php
/* 
 * templateテーブルのフィクスチャークラス
 */

class Fixture_Template extends Setuco_Test_Fixture_Abstract
{
    const TOP_ID = 1;
    const USER_CREATE_ID = 2;

    public function getColumns()
    {
        return array('id', 'account_id', 'title', 'file_name', 'explanation');
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    public function getFixtureBase()
    {
        return array(
              'account_id'      => Fixture_Account::ADMIN_ID,
            );
    }

    public function getDataOfTop()
    {
        return array('id' => self::TOP_ID, 'file_name' => Fixture_Account::ADMIN_ID . '_1', 'title' => 'TOPページのテンプレート', 'explanation' => 'TOPページのテンプレートです');
    }

    public function getDataOfUserCreate()
    {
        return array('id' => self::USER_CREATE_ID, 'account_id' => Fixture_Account::GENERAL_ID, 'file_name' => Fixture_Account::GENERAL_ID . '_1', 'title' => '一般ユーザーのテンプレート', 'explanation' => '一般ユーザーが作成したテンプレートです');
    }
}



