<?php
/**
 * 閲覧側のページを表示するコントローラーです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category    Setuco
 * @package     Default
 * @subpackage  Controller
 * @license     http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright   Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
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
     * pageサービスクラスのインスタンス
     *
     * @var Default_Model_Page
     *
     */
    private $_pageService = null;

    /**
     * categoryサービスクラスのインスタンス
     *
     * @var Default_Model_Category
     */
    private $_categoryService = null;

    /**
     * tagサービスクラスのインスタンス
     *
     * @var Default_Model_Tag
     */
    private $_tagService = null;

    /**
     * アクションの共通設定
     *
     * @return void
     * @author suzuki_mar akitsukada
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
        $this->_helper->redirector('index', 'index');
    }

    /**
     * キーワード検索結果を表示するアクション。
     * 
     * @return void
     * @author akitsukada
     * @todo 一覧には、記事本文の先頭15文字を表示する（現在はhtmlタグ等含めてcontentsのデータ先頭から単純に15文字。要検討）
     */
    public function searchAction()
    {

        $keyword = $this->_getParam('query');
        $currentPage = $this->_getPage();
        $searchResult = $this->_pageService->searchPages($keyword, $currentPage, self::LIMIT_GET_NEW_PAGE);
        $searchResultCount = $this->_pageService->countPagesByKeyword($keyword);

        if ($searchResultCount == 0) {
            // 検索結果が0件なら専用のビューを使用
            $this->_helper->viewRenderer('searchnot');
        } else {           
            
            $this->view->searchResult = $searchResult;

            // ページネータ設定
            $this->view->currentPage = $currentPage;
            $this->setPagerForView($searchResultCount, self::LIMIT_GET_NEW_PAGE);
        }

        $this->_pageTitle = "「{$keyword}」の検索結果";
        $this->view->resultCount = $searchResultCount;
        $this->view->keyword = $keyword;

    }

    /**
     * あるカテゴリーに属するページの一覧を表示する、
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
            $id = null;
        }

        $currentPage = $this->_getPage();
        $this->view->entries = $this->_pageService->getPagesByCategoryId($id, $currentPage);

        $category = array_pop($this->_categoryService->findCategory($id));
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
     * タグ名を検索して、該当するタグがつけられたページの一覧を表示する。
     *
     * @return void
     * @author akitsukada
     * @todo 一覧には、記事本文の先頭15文字を表示する（現在はhtmlタグ等含めてcontentsのデータ先頭から単純に15文字。要検討）
     */
    public function tagAction()
    {
        $id = $this->_getParam('id');

        if (!is_numeric($id)) {
            // 不正なIDが指定されたら閲覧側トップにリダイレクト（暫定仕様）
            $this->_helper->redirector('index', 'index');
        }

        $this->_helper->viewRenderer('search');

        $currentPage = $this->_getPage();
        $tag = array_pop($this->_tagService->findTag($id));

        $this->view->searchResult = $this->_pageService->getPagesByTagId($id, $currentPage, self::LIMIT_GET_NEW_PAGE);
        $this->view->resultCount = $this->_pageService->countPagesByTagId($id);
        $this->view->keyword = $tag['name'];
        
        $this->_pageTitle = "「{$tag['name']}」タグの記事";

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($this->_pageService->countPagesByTagId($id), self::LIMIT_GET_NEW_PAGE);
    }

    /**
     * IDを指定して記事を閲覧する。
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
        $page = array_pop($this->_pageService->findPage($id));
        $this->_pageTitle = $page['title'];
        $this->view->page = $page;

        // ページにつけられたタグ情報の取得とセット
        $this->view->tags = $this->_tagService->getTagsByPageId($id);
    }

}
