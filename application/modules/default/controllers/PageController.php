<?php
/**
 * 閲覧側のページを表示するコントローラーです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category    Setuco
 * @package     Default
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki-mar akitsukada
 */

/**
 * @category    Setuco
 * @package     Default
 * @subpackage  Controller
 * @author      suzuki-mar akitsukada
 */
class PageController extends Setuco_Controller_Action_DefaultAbstract
{
    /**
     * 新着記事表示用に標準で何件取得するか
     */
    const LIMIT_GET_NEW_PAGE = 10;

    /**
     * カテゴリ別検索で標準で何件取得するか
     */
    const LIMIT_GET_PAGE_BY_CATEGORY = 5;


    /**
     * サービスクラス
     */
    private $_pageService = null;

    private $_categoryService = null;
    private $_tagService = null;


    /**
     * アクションの共通設定
     *
     * @return void
     * @author suzuki_mar
     */
    public function init()
    {
        //モジュール間の共通の設定を実行
        parent::init();

        $this->_pageService = new Default_Model_Page();
        $this->_categoryService = new Default_Model_Category();
        $this->_tagService = new Default_Model_Tag();
    }

    /**
     * トップページのアクションです
     *
     * @return void
     * @author suzuki-mar
     * @todo 内容を実装する　現在はスケルトン
     */
    public function indexAction()
    {

    }

    /**
     * 検索結果を表示するアクションです
     * 
     * @return void
     * @author akitsukada
     * @todo 実装（現在スケルトン）
     */
    public function searchAction()
    {

        $keyword = $this->_getParam('query');
        $currentPage = $this->_getPage();
        $searchResult = $this->_pageService->search($keyword, $currentPage, self::LIMIT_GET_NEW_PAGE);
        $searchResultCount = $this->_pageService->countPagesByKeyword($keyword);

        $this->view->searchResult = $searchResult;
        $this->view->resultCount = $searchResultCount;
        $this->view->keyword = $keyword;

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($searchResultCount, self::LIMIT_GET_PAGE_BY_CATEGORY);

    }

    /**
     * あるカテゴリーに属するページの一覧を表示する
     *
     * @return void
     * @author akitsukada
     * @todo 一覧にはコンテンツの頭５行のみ表示する（現在とりあえず生コンテンツデータそのまま表示）
     */
    public function categoryAction()
    {

        $id = $this->_getParam('id');
        if (!is_numeric($id) && !is_null($id) || is_numeric($id) && $id < 0) {
            // 不正なIDが指定されたら閲覧側トップにリダイレクト（暫定仕様）
            $this->_helper->redirector('index', 'index');
        } elseif ($id == 0) {
            // カテゴリ未分類が指定されたとき（todo 仕様確定待ち）
            $id = null;
        }

        $currentPage = $this->_getPage();

        $this->_categoryService = new Default_Model_Category();

        $this->view->entries = $this->_pageService->getPagesByCategoryId($id, $currentPage);

        $category = array_pop($this->_categoryService->find($id));
        if (is_null($category['name'])) {
            $category['name'] = '未分類';
        }

        $this->view->category = $category;

        $this->_pageTitle = "「{$category['name']}」カテゴリーの記事";

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($this->_pageService->countPagesByCategoryId($id), self::LIMIT_GET_PAGE_BY_CATEGORY);

    }

    /**
     * あるタグがつけられたページの一覧を表示する
     *
     * @return void
     * @author akitsukada
     */
    public function tagAction()
    {
        $id = $this->_getParam('id');

        if (!is_numeric($id)) {
            // 不正なIDが指定されたら閲覧側トップにリダイレクト（暫定仕様）
            $this->_helper->redirector('index', 'index');
        }

        $currentPage = $this->_getPage();
        $tag = array_pop($this->_tagService->find($id));
        $this->view->entries = $this->_pageService->getPagesByTagId($id, $currentPage, self::LIMIT_GET_NEW_PAGE);
        $this->view->tag = $tag;
        $this->view->pageCount = $this->_pageService->countPagesByTagId($id);
        $this->_pageTitle = "「{$tag['name']}」タグの記事";

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($this->_pageService->countPagesByTagId($id), self::LIMIT_GET_NEW_PAGE);
    }

    /**
     * ページを閲覧する
     *
     * @return void
     * @author akitsukada
     */
    public function showAction()
    {
        $id = $this->_getParam('id');

        if (!is_numeric($id)) {
            // 不正なIDが指定されたら閲覧側トップにリダイレクト（暫定仕様）
            $this->_helper->redirector('index', 'index');
        }

        // 記事情報の取得とセット
        $page = array_pop($this->_pageService->find($id));
        $this->_pageTitle = $page['title'];
        $this->view->page = $page;

        // ページにつけられたタグ情報の取得とセット
        $this->view->tags = $this->_tagService->getTagsByPageId($id);
    }

}
