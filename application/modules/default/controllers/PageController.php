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
    private $_service = null;

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

        $this->_service = new Default_Model_Page;
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
            // 不正なIDが指定されたら閲覧側トップにリダイレクト
            $this->_helper->redirector('index', 'index');
        } elseif ($id == 0) {
            $id = null;
        }

        $currentPage = $this->_getParam('page', 1);
        if (!is_numeric($currentPage)) {
            $currentPage = 1;
        }

        $categoryService = new Default_Model_Category();

        $this->view->entries = $this->_service->getPagesByCategory($id, $currentPage);
        $this->view->category = $categoryService->getCategoryById($id);

        // ページネーター用の設定
        $this->view->currentPage = $currentPage;
        
        // @todo setPagerForView がadminからcommonに移されるの待ち
        //$this->setPagerForView($this->_service->countPagesByCategory($id), 5);

    }

    /**
     * あるタグがつけられたページの一覧を表示する
     *
     * @return void
     * @author akitsukada
     * @todo 実装（現在スケルトン）
     */
    public function tagAction()
    {

    }

    /**
     * ページを閲覧する
     *
     * @return void
     * @author akitsukada
     * @todo 実装（現在スケルトン）
     */
    public function showAction()
    {

    }

}
