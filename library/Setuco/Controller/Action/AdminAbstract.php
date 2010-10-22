<?php
/**
 * adminモジュールの共通のコントローラーです
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @category    Setuco
 * @package     Setuco
 * @subpackage  Controller_Action
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_AdminAbstract extends Setuco_Controller_Action_Abstract
{
    /**
     * ナビゲーション
     *
     * @var Zend_Navigation
     */
    protected $_navigation;

    /**
     * 一覧ページで、1ページあたり何件のデータを表示するか
     */
    const PAGE_LIMIT = 10;

    /**
     * adminモジュールコントローラの初期処理です。
     *
     * @return void
     * @author suzuki-mar
     */
    public function init()
    {
        parent::init();
        $this->_navigation = $this->_initNavigation();
        $this->_initHeader();
    }

    /**
     * ナビゲーションの設定情報を初期化します。
     *
     * @return Zend_Navigation
     * @author charlesvineyard
     */
    protected function _initNavigation()
    {
        $navigationConfig = new Zend_Config_Xml($this->_getModulePath()
                . 'configs/navigation.xml', 'nav', true);
        return new Zend_Navigation($navigationConfig);
    }

    /**
     * ヘッダーに関する初期処理です。
     *
     * @return Zend_Navigation
     * @author charlesvineyard
     */
    protected function _initHeader()
    {
        $auth = Zend_Auth::getInstance();
        if (! $auth->hasIdentity()) {
            return;
        }
        
        $loginId = $auth->getIdentity();
        $accountService = new Admin_Model_Account();
        $account = $accountService->load($loginId);
        $this->view->nickName = $account['nickname'];
        
        $siteService = new Admin_Model_Site();
        $site = $siteService->getSiteInfo();
        $this->view->siteName = $site['name'];
        $this->view->siteUrl  = $site['url'];
    }

    /**
     * アクションメソッドが呼ばれるの直前の処理です。
     *
     * @return void
     * @author charlesvineyard
     */
    public function preDispatch()
    {
        $this->view->headTitle($this->_chooseHeadTitle());
    }

    /**
     * リクエスト中のページのタイトルを取得します。
     *
     * @return string|null タイトルが設定されていればタイトル、なければ null を返します。
     * @author charlesvineyard
     */
    protected function _chooseHeadTitle()
    {
        $currentNavController = $this->_navigation->findByController(
                $this->getRequest()->getControllerName());
        if(! $currentNavController) {
            return null;
        }
        $currentNavAction = $currentNavController->findByAction(
                $this->getRequest()->getActionName());
        if (! $currentNavAction) {
            return null;
        }
        return $currentNavAction->getTitle();
    }

    /**
     * ページネーターで使う現在の（クリックされた）ページ番号を取得するメソッドです
     *
     * @return int 現在ページネーターで表示すべきページ番号
     * @author akitsukada
     */
    protected function _getPage()
    {
        // URLからページ番号の指定を得る ( デフォルトは1 )
        $currentPage = $this->_getParam('page');
        if (!is_numeric($currentPage)) {
            $currentPage = 1;
        }
        return $currentPage;
    }



    /**
     * ページャーの設定をして、ビューで使用できるようにする
     *
     * @param int 最大何件のデータが該当したのか
     * @param int[option] 一ページあたりに何件のデータを表示するのか
     * @return void
     * @author suzuki-mar
     */
    public function setPagerForView($max, $limit = null)
    {
        //数値ではない場合は数値にキャストする 数字ではエラーなので
        if(is_string($max)) {
            $max = (int) $max;
        }

        //共通のページャーの設定をする
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('common/pager.phtml');

        //現在のページ番号を取得する
        $page = $this->_getParam('page', 1);

        //現在のページ番号を渡す
        $this->view->page = $page;

       //指定がなければ、デフォルトを使用する
       if (is_null($limit)) {
       	 $limit = self::PAGE_LIMIT;
       }

        //ページャークラスを生成する
        $paginator = Zend_Paginator::factory($max);
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($limit);

        //viewでpaginationControlを使用しなくても、表示できるようにする
        $paginator->setView($this->view);

        //ページャーをviewで使用できるようにする
        $this->view->paginator = $paginator;

    }

    /**
     * フラッシュメッセージがアクションヘルパーに設定されていればビューにセットして可視化します。
     *
     * @param  string $paramName ビューにセットする変数名。デフォルトは "flashMessage"。
     * @return void
     * @author charlesvineyard
     */
    protected function _visibleFlashMessage($paramName = 'flashMessage')
    {
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->$paramName = $flashMessages[0];
        }
    }
}
