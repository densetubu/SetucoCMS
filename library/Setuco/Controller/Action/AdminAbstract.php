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
abstract class Setuco_Controller_Action_AdminAbstract extends Setuco_Controller_Action_ErrorAbstract
{
    /**
     * ページのタイトル
     * 設定ファイルからとれないときだけ指定。
     *
     * @var string
     */
    protected $_pageTitle;

    /**
     * ナビゲーション
     *
     * @var Zend_Navigation
     */
    protected $_navigation;

    /**
     * 一覧ページで、1ページあたり何件のデータを表示するか 削除するので使用しない
     * @todo 定数の削除 検討課題のチケットチェック時に削除する
     */
    const PAGE_LIMIT = 10;

    /**
     * 一覧ページで、1ページあたり何件のデータを表示するか
     * @var int
     * @todo PAGE_LIMITの削除
     */
    protected $_pageLimit = 10;


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
        $account = $accountService->findAccountByLoginId($loginId);
        $this->view->nickName = $account['nickname'];

        $siteService = new Admin_Model_Site();
        $site = $siteService->getSiteInfo();
        $this->view->siteName = $site['name'];
        $this->view->siteUrl  = $site['url'];
    }

    /**
     * アクションメソッドが呼ばれた後の処理です。
     *
     * @return void
     * @author charlesvineyard
     */
    public function postDispatch()
    {
        $this->view->headTitle(($this->_pageTitle === null)
            ? $this->_chooseHeadTitle() : $this->_pageTitle,
            Zend_View_Helper_Placeholder_Container_Abstract::SET);
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
     * フォームエレメントの共通設定をする
     * requiredなどの設定をする
     *　第２引数で、設定しない項目を指定できる
     *
     * @param Zend_Form_Element $element　共通の設定をするフォームエレメントクラス
     * @param array[option] キャンセル名前のキーにfalseを渡すと設定しないことができる
     * @return void
     * @author suzuki-mar
     */
    protected function _addFormElementCommonOptions(&$element, $cancelOptions = null)
    {
        if( !(isset($cancelOptions['required']) && $cancelOptions['required'] === false)) {
            $element->setRequired();
        }

        $element->addFilter(new Setuco_Filter_FullWidthStringTrim());
    }


}
