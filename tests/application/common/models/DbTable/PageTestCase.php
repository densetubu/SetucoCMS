<?php

/**
 *
 * @author suzuki-mar
 */
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';


class PageTestCase extends Setuco_Test_PHPUnit_DatabaseTestCase
{

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
    }

    public function test_loadData()
    {
        $this->assertTrue(true);
    }

//    public function testloadPagesByKeyword4Pager_キーワードから記事を検索する_検索するのはすべての項目()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_TITLE_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_CONTENTS_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_OUTLINE_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_TAG_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_ACCOUNT_ID),
//        );
//
//        $this->assertEquals($expects, $this->_dao->loadPagesByKeyword4Pager($this->_params));
//    }
//
//    public function testloadPagesByKeyword4Pager_キーワードが空で検索の場合は全件検索する()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_TITLE_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_MULTI_KEYWORD_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_CONTENTS_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_OUTLINE_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_TAG_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_ACCOUNT_ID),
//
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_ACCOUNT_ONLY_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_HTML_TAG_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_NOTAG_ID),
//        );
//
//        $params = $this->_params;
//        $params->setDaoParams(array('keyword' => ''));
//        $params->setDaoParams(array('tagIds' => array()));
//
//        $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//
//    }
//
//    public function testloadPagesByKeyword4Pager_タグだけで検索する()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_TAG_ID),
//        );
//
//        $params = $this->_params;
//        $params->setDaoParams(array('keyword' => 'hogefuga'));
//        $params->setDaoParams(array('targetColumns' => array('tag')));
//
//        $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//    }
//
//    public function testloadPagesByKeyword4Pager_タグでは検索しない()
//    {
//       $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_TITLE_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_CONTENTS_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_OUTLINE_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_ACCOUNT_ID),
//       );
//
//       $params = $this->_params;
//       $params->setDaoParams(array('targetColumns' => array('title', 'contents', 'outline')));
//       $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//
//    }
//
//    public function testloadPagesByKeyword4Pager_指定したアカウントの記事だけを検索する()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_ACCOUNT_ID),
//       );
//
//       $params = $this->_params;
//       $params->setDaoParams(array('refinements' => array('account_id' => 3)));
//       $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//    }
//
//    public function testloadPagesByKeyword4Pager_指定したアカウントIDだけで検索する_キーワードはなし()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_ACCOUNT_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_ACCOUNT_ONLY_ID),
//
//       );
//
//       $params = $this->_params;
//       $params->setDaoParams(array(
//               'refinements'    => array('account_id' => 3),
//               'targetColumns'  => array(),
//               'keyword'        => ''
//               ));
//
//       $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//    }
//
//    public function testloadPagesByKeyword4Pager_指定したアカウントIDとカテゴリーIDで検索する_キーワードはなし()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_ACCOUNT_ONLY_ID),
//
//       );
//
//       $params = $this->_params;
//       $params->setDaoParams(array(
//               'refinements'    => array('account_id' => 3, 'category_id' => 1),
//               'targetColumns'  => array(),
//               'keyword'        => ''
//               ));
//
//       $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//    }
//
//    public function testloadPagesByKeyword4Pager_複数キーワード検索をする_タイトル()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_TITLE_ID),
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_MULTI_KEYWORD_ID),
//        );
//
//        $params = $this->_params;
//        $params->setDaoParams(array('keyword' => 'タイトル　検索'));
//        $params->setDaoParams(array('tagIds' => array()));
//
//        $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//    }
//
//
//    public function testloadPagesByKeyword4Pager_複数キーワード検索をする_コンテンツ()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_CONTENTS_ID),
//        );
//
//        $params = $this->_params;
//        $params->setDaoParams(array('keyword' => 'コンテンツ　検索'));
//        $params->setDaoParams(array('tagIds' => array()));
//
//        $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//    }
//
//
//    public function testloadPagesByKeyword4Pager_複数キーワード検索をする_アウトライン()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_OUTLINE_ID),
//        );
//
//        $params = $this->_params;
//        $params->setDaoParams(array('keyword' => 'アウト ライン　検索'));
//        $params->setDaoParams(array('tagIds' => array()));
//
//        $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//    }
//
//    public function testloadPagesByKeyword4Pager_コンテンツの場合タグは検索しないか()
//    {
//        $expects = array(
//            Fixture_Page::getFixtureData(Fixture_Page::DATA_NOTAG_ID),
//        );
//
//        $params = $this->_params;
//        $params->setDaoParams(array('keyword' => 'p'));
//        $params->setDaoParams(array('tagIds' => array()));
//
//        $this->assertRowDatas($expects, $this->_dao->loadPagesByKeyword4Pager($params));
//    }

}



