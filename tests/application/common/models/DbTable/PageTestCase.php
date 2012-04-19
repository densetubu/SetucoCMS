<?php

/**
 *
 * @author suzuki-mar
 */
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';


class PageTestCase extends Setuco_Test_PHPUnit_DatabaseTestCase
{

    const DATA_TITLE_ID = 1;
    const DAtA_MULTI_KEYWORD_ID = 2;
    const DATA_CONTENTS_ID = 3;
    const DATA_OUTLINE_ID = 4;
    const DATA_TAG_ID = 5;
    const DATA_ACCOUNT_ID = 6;
    const DATA_ACCOUNT_ONLY_ID = 7;
    const DATA_NOTAG_ID = 9;

    

    public function setup()
    {
        parent::setup();
        
        $this->_dao = new Common_Model_DbTable_Page($this->getAdapter());

        $keyword = '検索して';
        $tagIds = array(1, 2);
        $pageNumber = 1;
        $limit = 10;
        $targetColumns = array('title', 'contents', 'outline', 'tag');
        $refinements = array();
        $sortColumn = 'create_date';
        $order      = 'desc';
        $searchOperator = 'AND';

        $this->_params = new Common_Model_Page_Param($keyword, $tagIds, $pageNumber, $limit, $targetColumns, $refinements, $sortColumn, $order, $searchOperator);

        $this->_baseExpects = array(
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

    private function _getExpectsPageData($id, $values = array())
    {
        $expect = $this->_baseExpects;
        $expect['id'] = $id;

        return array_merge($expect, $values);
    }

    public function testloadPagesByKeyword4Pager_キーワードから記事を検索する_検索するのはすべての項目()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_TITLE_ID, array('title' => 'タイトルで検索して')),
            $this->_getExpectsPageData(self::DATA_CONTENTS_ID, array('contents' => 'コンテンツで検索して')),
            $this->_getExpectsPageData(self::DATA_OUTLINE_ID, array('outline' => 'アウトラインで検索して')),
            $this->_getExpectsPageData(self::DATA_TAG_ID),
            $this->_getExpectsPageData(self::DATA_ACCOUNT_ID,
                    array(
                        'nickname'      => '検索する人',
                        'title'         => 'アカウントで検索して',
                        'account_id'    => 3
                        )),
        );

        $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($this->_params));
    }

    public function testloadPagesByKeyword4Pager_タグだけで検索する()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_TAG_ID),
        );

        $params = $this->_params;
        $params->setDaoParams(array('keyword' => 'hogefuga'));
        $params->setDaoParams(array('targetColumns' => array('tag')));

        $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));
    }

    public function testloadPagesByKeyword4Pager_タグでは検索しない()
    {
       $expects = array(
            $this->_getExpectsPageData(self::DATA_TITLE_ID, array('title' => 'タイトルで検索して')),
            $this->_getExpectsPageData(self::DATA_CONTENTS_ID, array('contents' => 'コンテンツで検索して')),
            $this->_getExpectsPageData(self::DATA_OUTLINE_ID, array('outline' => 'アウトラインで検索して')),
            $this->_getExpectsPageData(self::DATA_ACCOUNT_ID,
                    array(
                        'nickname'      => '検索する人',
                        'title'         => 'アカウントで検索して',
                        'account_id'    => 3
                        )),
       );

       $params = $this->_params;
       $params->setDaoParams(array('targetColumns' => array('title', 'contents', 'outline')));
       $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));

    }

    public function testloadPagesByKeyword4Pager_指定したアカウントの記事だけを検索する()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_ACCOUNT_ID,
                    array(
                        'nickname'      => '検索する人',
                        'title'         => 'アカウントで検索して',
                        'account_id'    => 3
                        )),
       );
 
       $params = $this->_params;
       $params->setDaoParams(array('refinements' => array('account_id' => 3)));
       $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));
    }

    public function testloadPagesByKeyword4Pager_指定したアカウントIDだけで検索する_キーワードはなし()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_ACCOUNT_ID,
                    array(
                        'nickname'      => '検索する人',
                        'title'         => 'アカウントで検索して',
                        'account_id'    => 3
                        )),
            $this->_getExpectsPageData(self::DATA_ACCOUNT_ONLY_ID,
                    array(
                        'nickname'      => '検索する人',
                        'account_id'    => 3,
                        'category_name' => 'test',
                        'category_id'   => 1,
                        )),

       );

       $params = $this->_params;
       $params->setDaoParams(array(
               'refinements'    => array('account_id' => 3),
               'targetColumns'  => array(),
               'keyword'        => ''
               ));
       
       $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));
    }
    
    public function testloadPagesByKeyword4Pager_指定したアカウントIDとカテゴリーIDで検索する_キーワードはなし()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_ACCOUNT_ONLY_ID,
                    array(
                        'nickname'      => '検索する人',
                        'account_id'    => 3,
                        'category_name' => 'test',
                        'category_id'   => 1,
                        )),

       );

       $params = $this->_params;
       $params->setDaoParams(array(
               'refinements'    => array('account_id' => 3, 'category_id' => 1),
               'targetColumns'  => array(),
               'keyword'        => ''
               ));
       
       $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));
    }

    public function testloadPagesByKeyword4Pager_複数キーワード検索をする_タイトル()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_TITLE_ID, array('title' => 'タイトルで検索して')),
            $this->_getExpectsPageData(self::DAtA_MULTI_KEYWORD_ID, array('title' => 'タイトルで検索しないで')),
        );

        $params = $this->_params;
        $params->setDaoParams(array('keyword' => 'タイトル　検索'));
        $params->setDaoParams(array('tagIds' => array()));

        $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));
    }


    public function testloadPagesByKeyword4Pager_複数キーワード検索をする_コンテンツ()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_CONTENTS_ID, array('contents' => 'コンテンツで検索して')),
        );

        $params = $this->_params;
        $params->setDaoParams(array('keyword' => 'コンテンツ　検索'));
        $params->setDaoParams(array('tagIds' => array()));

        $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));
    }


    public function testloadPagesByKeyword4Pager_複数キーワード検索をする_アウトライン()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_OUTLINE_ID, array('outline' => 'アウトラインで検索して')),
        );

        $params = $this->_params;
        $params->setDaoParams(array('keyword' => 'アウト ライン　検索'));
        $params->setDaoParams(array('tagIds' => array()));

        $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));
    }

    public function testloadPagesByKeyword4Pager_コンテンツの場合タグは検索しないか()
    {
        $expects = array(
            $this->_getExpectsPageData(self::DATA_NOTAG_ID, array('contents' => 'ppp')),
        );

        $params = $this->_params;
        $params->setDaoParams(array('keyword' => 'p'));
        $params->setDaoParams(array('tagIds' => array()));

        $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($params));
    }

}



