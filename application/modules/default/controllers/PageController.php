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
     * キーワード検索で何件取得するか
     *
     * @var int
     */
    const LIMIT_PAGE_SEARCH = self::PAGE_LIMIT;


    /**
     * カテゴリ別検索で何件取得するか
     *
     * @var int
     */
    const LIMIT_PAGE_CATEGORY = 5;

    /**
     * タグ別検索で何件取得するか
     *
     * @var int
     */
    const LIMIT_PAGE_TAG = self::LIMIT_PAGE_SEARCH;


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
     */
    public function searchAction()
    {

        $keyword = $this->_getParam('query');
        $keyword = Zend_Filter::filterStatic($keyword, 'RestParamDecode', array (), 'Setuco_Filter');
        $keyword = Zend_Filter::filterStatic($keyword, 'FullWidthStringTrim', array (), 'Setuco_Filter');
        $currentPage = $this->_getPageNumber();
        $searchResultCount = $this->_pageService->countPagesByKeyword(
                $keyword, null, array('status' => Setuco_Data_Constant_Page::STATUS_RELEASE));

        if ($searchResultCount == 0) {
            // 検索結果が0件の場合ビュー切り替え
            $this->_helper->viewRenderer('searchnot');
        } else {

            $searchResult = $this->_pageService->searchPages(
                    $keyword, $currentPage, self::LIMIT_PAGE_SEARCH, null,
                    array('status' => Setuco_Data_Constant_Page::STATUS_RELEASE));
            $date = new Zend_Date();
            foreach($searchResult as $key => $entry) {
                $date->set($entry['update_date'], Zend_Date::ISO_8601);
                $searchResult[$key]['update_date'] = $date->toString('Y年MM月dd日');
                $searchResult[$key]['contents'] = mb_substr(strip_tags($entry['contents']), 0, 15, 'UTF-8');
            }

            $this->view->searchResult = $searchResult;
            // ページネータ設定
            $this->view->currentPage = $currentPage;
            $this->setPagerForView($searchResultCount, self::LIMIT_PAGE_SEARCH);
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
     * @todo 一覧にはコンテンツの頭５行のみ表示する（現在とりあえず100文字表示）
     */
    public function categoryAction()
    {

        $id = $this->_getParam('id');

        if ($id === Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE || $id === '0') { // @todo 未分類カテゴリの指定方法確定
            // id='uncategorized'は未分類扱いとする
            $id = null;
        } elseif (!is_numeric($id) && !is_null($id) || is_numeric($id) && $id < 0) {
            // 不正なIDが指定されたら例外発生させる（暫定仕様）
            throw new Zend_Exception("不正なカテゴリID");
        } else {
            // 正しいID（1以上の整数文字）が指定されたらintにしておく
            $id = (int)$id;
        }

        $currentPage = $this->_getPageNumber();
        $entries = $this->_pageService->findPagesByCategoryId(
            $id, Setuco_Data_Constant_Page::STATUS_RELEASE, $currentPage, self::LIMIT_PAGE_CATEGORY);
        $date = new Zend_Date();
        foreach ($entries as $cnt => $entry) {
            $entries[$cnt]['contents'] = mb_substr(strip_tags($entry['contents']), 0, 100, 'UTF-8');
            $date->set($entry['update_date'], Zend_Date::ISO_8601);
            $entries[$cnt]['update_date'] = $date->toString('Y/MM/dd HH:mm');
        }
        $this->view->entries = $entries;

        if (is_null($id)) {
            $category = Setuco_Data_Constant_Category::UNCATEGORIZED_INFO();
        } else {
            $category = $this->_categoryService->findCategory($id);
        }
        
        $this->view->category = $category;
        $this->_pageTitle = "「{$category['name']}」カテゴリーのページ";

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        $this->setPagerForView($this->_pageService->countPagesByCategoryId($id, Setuco_Data_Constant_Page::STATUS_RELEASE), self::LIMIT_PAGE_CATEGORY);

    }

    /**
     * タグ名を検索して、該当するタグがつけられたページの一覧を表示する。
     *
     * @return void
     * @author akitsukada
     * @todo 一覧には、ページ本文の先頭15文字を表示する（現在はhtmlタグ等含めてcontentsのデータ先頭から単純に15文字。要検討）
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

            // 検索結果が0件の場合 該当ページなしのビュー
            $this->_helper->viewRenderer('searchnot');

        } else {

            // 検索結果が0件の場合 検索結果表示ビュー
            $this->_helper->viewRenderer('search');
            $searchResult = $this->_pageService->findPagesByTagId($id, $currentPage, self::LIMIT_PAGE_TAG);

            $date = new Zend_Date();
            foreach($searchResult as $key => $entry) {
                $date->set($entry['update_date'], Zend_Date::ISO_8601);
                $searchResult[$key]['update_date'] = $date->toString('Y年MM月dd日');
                $searchResult[$key]['contents'] = mb_substr(strip_tags($entry['contents']), 0, 15, 'UTF-8');
            }

            $this->view->searchResult = $searchResult;
            // ページネータ設定
            $this->view->currentPage = $currentPage;
            $this->setPagerForView($searchResultCount, self::LIMIT_PAGE_TAG);
        }

        $this->view->resultCount = $searchResultCount;
        $this->view->keyword = $keyword;
        $this->_pageTitle = "「{$keyword}」タグのページ";

    }

    /**
     * IDを指定してページを閲覧する。
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

        // ページ情報の取得
        $page = $this->_pageService->findPage($id);

        if (is_null($page)) {
            throw new Setuco_Exception('ページが存在しません', 404);
        }
        if ($page['status'] != Setuco_Data_Constant_Page::STATUS_RELEASE) {
            throw new Setuco_Exception('ページが存在しません', 404);
        }

        // カテゴリー情報の取得
        $categoryId = $page['category_id'];
        if (is_null($categoryId)) {
            $category['id'] = Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE;
            $category['name'] = Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
            $category['parent_id'] = Setuco_Data_Constant_Category::NO_PARENT_ID;
        } else {
            $category = $this->_categoryService->findCategory($categoryId);
        }

        // 日時情報のフォーマット編集
        $date = new Zend_Date();
        $page['update_date'] = $date->set($page['update_date'], Zend_Date::ISO_8601)->toString('Y/MM/dd HH:mm');
        $page['create_date'] = $date->set($page['create_date'], Zend_Date::ISO_8601)->toString('Y/MM/dd HH:mm');

        $this->_pageTitle = $page['title'];
        $this->view->category = $category;
        $this->view->page = $page;

        // ページにつけられたタグ情報の取得とセット
        $this->view->tags = $this->_tagService->findTagsByPageId($id);
    }

}
