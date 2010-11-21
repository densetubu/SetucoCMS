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
        $currentPage = $this->_getPageNumber();
        $searchResultCount = $this->_pageService->countPagesByKeyword($keyword);

        if ($searchResultCount == 0) {

            // 検索結果が0件の場合ビュー切り替え
            $this->_helper->viewRenderer('searchnot');

        } else {

            $searchResult = $this->_pageService->searchPages($keyword, $currentPage, self::LIMIT_GET_NEW_PAGE);
            $date = new Zend_Date();
            foreach($searchResult as $key => $entry) {
                $date->set($entry['update_date'], Zend_Date::ISO_8601);
                $searchResult[$key]['update_date'] = $date->toString('Y年MM月dd日');
                $searchResult[$key]['contents'] = mb_substr(strip_tags($entry['contents']), 0, 15, 'UTF-8');
            }

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

        $currentPage = $this->_getPageNumber();
        $this->view->entries = $this->_pageService->findPagesByCategoryId($id, $currentPage, self::LIMIT_GET_PAGE_BY_CATEGORY);

        $category = $this->_categoryService->findCategory($id);
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
        
        $currentPage = $this->_getPageNumber();
        $searchResultCount = $this->_pageService->countPagesByTagId($id);
        $tag = $this->_tagService->findTag($id);
        $keyword = is_null($tag['name']) ? '？？？(該当タグなし)' : $tag['name'];

        if ($searchResultCount == 0) {
                
            // 検索結果が0件の場合 該当記事なしのビュー
            $this->_helper->viewRenderer('searchnot');

        } else {

            // 検索結果が0件の場合 検索結果表示ビュー
            $this->_helper->viewRenderer('search');
            $searchResult = $this->_pageService->findPagesByTagId($id, $currentPage, self::LIMIT_GET_NEW_PAGE);
            
            $date = new Zend_Date();
            foreach($searchResult as $key => $entry) {
                $date->set($entry['update_date'], Zend_Date::ISO_8601);
                $searchResult[$key]['update_date'] = $date->toString('Y年MM月dd日');
                $searchResult[$key]['contents'] = mb_substr(strip_tags($entry['contents']), 0, 15, 'UTF-8');
            }

            $this->view->searchResult = $searchResult;
            // ページネータ設定
            $this->view->currentPage = $currentPage;
            $this->setPagerForView($searchResultCount, self::LIMIT_GET_NEW_PAGE);
        }

        $this->view->resultCount = $searchResultCount;
        $this->view->keyword = $keyword;
        $this->_pageTitle = "「{$keyword}」タグの記事";

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

        // 記事情報の取得
        $page = $this->_pageService->findPage($id);
        
        if (is_null($page)) {
            // idに該当するページなし（通常おこらない不適切なアクセス ex.URL手入力など）
            // @todo 仕様策定と実装
        }

        // カテゴリー情報の取得
        $categoryId = $page['category_id'];
        $category = $this->_categoryService->findCategory($categoryId);
        if (is_null($category['name'])) {
            $category['name'] = '未分類';
        }

        // 日時情報のフォーマット編集
        $date = new Zend_Date();
        $page['update_date'] = $date->set($page['update_date'], Zend_Date::ISO_8601)->toString('Y/MM/dd HH:mm');
        $page['create_date'] = $date->set($page['create_date'], Zend_Date::ISO_8601)->toString('Y/MM/dd HH:mm');

        $this->_pageTitle = $page['title'];
        $this->view->category = $category;
        $this->view->page = $page;

        // ページにつけられたタグ情報の取得とセット
        $this->view->tags = $this->_tagService->getTagsByPageId($id);
    }

}
