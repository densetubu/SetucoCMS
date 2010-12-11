<?php

/**
 * 閲覧側のトップページのコントローラーです。
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
 * @author      suzuki_mar
 */



/**
 * @category    Setuco
 * @package     Default
 * @subpackage  Controller
 * @author      suzuki_mar
 */
class IndexController extends Setuco_Controller_Action_DefaultAbstract
{
    /**
     * 新着ページ表示用に標準で何件取得するか
     *
     * @var string
     */
    const LIMIT_GET_NEW_PAGE = 10;

    /**
     * ページのサービスクラス
     *
     * @var Default_Model_Page
     *
     */
    private $_pageService = null;

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

    }

    /**
     * トップページのアクションです
     *
     * @return void
     * @author suzuki-mar
     */
    public function indexAction()
    {
        //新着ページを取得する
        $this->view->newPages = $this->_pageService->findLastUpdatedPages(Setuco_Data_Constant_Module_Default::LIMIT_GET_NEW_PAGE);
    }



}


