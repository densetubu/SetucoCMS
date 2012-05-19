<?php

/* 
 * pageテーブルのフィクスチャークラス
 */

class Fixture_Page extends Setuco_Test_Fixture_Abstract
{
    const TITLE_ID = 1;
    const TITLE_TITLE = "タイトルで検索して";

    const MULTI_KEYWORD_ID = 2;
    const MULTI_KEYWORD_TITLE = 'タイトルで検索しないで';

    const CONTENTS_ID = 3;
    const CONTENTS_TITLE = 'contents_search';

    const OUTLINE_ID = 4;
    const OUTLINE_TITLE = 'outline_search';

    const TAG_ID = 5;
    const TAG_TITLE = 'tag_search';

    const ACCOUNT_ID = 6;
    const ACCOUNT_TITLE = 'アカウントで検索して';

    const ACCOUNT_ONLY_ID = 7;
    const ACCOUNT_ONLY_TITLE = 'アカウントだけで検索して';

    const HTML_TAG_ID = 8;
    const HTML_TAG_TITLE = 'html_tag_title';

    const NO_HTML_TAG_ID = 9;
    const NO_HTML_TAG_TITLE = 'no_tag_title';

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
    public function getFixtureBase()
    {
        return array(
              'id'              => 'id',
              'title'           => 'title',
              'contents'        => 'contents',
              'outline'         => 'outline',
              'status'          => Setuco_Data_Constant_Page::STATUS_DRAFT,
              'category_id'     => null,
              'account_id'      => Fixture_Account::ADMIN_ID,
              'create_date'     => '2012-04-03 08:48:44',
              'update_date'     => '2012-04-03 08:48:44',
            );
    }

    public function getDataOfTitle()
    {
        return array('id' => self::TITLE_ID, 'title' => self::TITLE_TITLE);
    }

    public function getDataOfMulti_Keyword()
    {
        return array('id' => self::MULTI_KEYWORD_ID, 'title' => self::MULTI_KEYWORD_TITLE);
    }

    public function getDataOfContents()
    {
        return array('id' => self::CONTENTS_ID, 'title' => self::CONTENTS_TITLE, 'contents' => 'コンテンツで検索して');
    }

    public function getDataOfOutline()
    {
        return array('id' => self::OUTLINE_ID, 'title' => self::OUTLINE_TITLE, 'outline' => 'アウトラインで検索して');
    }

    public function getDataOfTag()
    {
        return array('id' => self::TAG_ID, 'title' => self::TAG_TITLE);
    }

    public function getDataOfAccount()
    {
        return array(
                        'id'            => self::ACCOUNT_ID,
                        'title'         => self::ACCOUNT_TITLE,
                        'account_id'    => Fixture_Account::TARGET_ID
                );
    }

    public function getDataOfAccount_Only()
    {
        return array(
                        'id'            => self::ACCOUNT_ONLY_ID,
                        'account_id'    => Fixture_Account::TARGET_ID,
                        'category_id'   => Fixture_Category::TEST_ID,
                        'title'         => self::ACCOUNT_ONLY_TITLE,
                );
    }

    public function getDataOfHtml_Tag()
    {
        return array('id' => self::HTML_TAG_ID, 'contents' => '<p>hoge</p>', 'title' => self::HTML_TAG_ID);
    }

    public function getDataOfNotag()
    {
        return array('id' => self::NO_HTML_TAG_ID, 'contents' => 'ppp', 'title' => self::NO_HTML_TAG_TITLE);
    }

}

