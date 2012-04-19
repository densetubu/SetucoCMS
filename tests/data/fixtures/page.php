<?php

/* 
 * pageテーブルのフィクスチャークラス
 */

class Fixture_Page extends Setuco_Test_Fixture_Abstract
{
    const TITLE_ID = 1;
    const MULTI_KEYWORD_ID = 2;
    const CONTENTS_ID = 3;
    const OUTLINE_ID = 4;
    const TAG_ID = 5;
    const ACCOUNT_ID = 6;
    const ACCOUNT_ONLY_ID = 7;
    const HTML_TAG_ID = 8;
    const NOTAG_ID = 9;

    public function getColumns()
    {
        return array(
            'id', 'title', 'contents', 'outline', 'status', 'category_id',
            'account_id', 'create_date', 'update_date'
            );
    }

    /**
     * フィクスチャーのベースを取得する
     *
     * @return array フィクスチャーのベース
     */
    protected function _getFixtureBase()
    {
        return array(
              'id'              => 'id',
              'title'           => 'search',
              'contents'        => 'search',
              'outline'         => 'search',
              'status'          => 1,
              'category_id'     => -1,
              'account_id'      => 1,
              'create_date'     => '2012-04-03 08:48:44',
              'update_date'     => '2012-04-03 08:48:44',
              'category_name'   => 'no_parent',
              'nickname'        => 'setuco',
            );
    }

    public function getDataOfTitle()
    {
        return array('id' => self::TITLE_ID, 'title' => 'タイトルで検索して');
    }

    public function getDataOfMulti_Keyword()
    {
        return array('id' => self::MULTI_KEYWORD_ID, 'title' => 'タイトルで検索しないで');
    }

    public function getDataOfContents()
    {
        return array('id' => self::CONTENTS_ID, 'contents' => 'コンテンツで検索して');
    }

    public function getDataOfOutline()
    {
        return array('id' => self::OUTLINE_ID, 'outline' => 'アウトラインで検索して');
    }

    public function getDataOfTag()
    {
        return array('id' => self::TAG_ID);
    }

    public function getDataOfAccount()
    {
        return array(
                        'id'            => self::ACCOUNT_ID,
                        'nickname'      => '検索する人',
                        'title'         => 'アカウントで検索して',
                        'account_id'    => 3
                );
    }

    public function getDataOfAccount_Only()
    {
        return array(
                        'id'            => self::ACCOUNT_ONLY_ID,
                        'nickname'      => '検索する人',
                        'account_id'    => 3,
                        'category_name' => 'test',
                        'category_id'   => 1,
                );
    }

    public function getDataOfHtml_Tag()
    {
        return array('id' => self::HTML_TAG_ID, 'contents' => '<p>hoge</p>');
    }

    public function getDataOfNotag()
    {
        return array('id' => self::NOTAG_ID, 'contents' => 'ppp');
    }

}

