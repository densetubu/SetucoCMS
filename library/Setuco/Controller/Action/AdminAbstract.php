<?php
/**
 * adminモジュールの共通のコントローラーです
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
 * @package    Setuco
 * @subpackage Controller_Action
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_AdminAbstract extends Setuco_Controller_Action_Abstract
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
     * @author charlesvineyard suzuki-mar
     */
    protected function _initHeader()
    {
        $auth = new Admin_Model_Auth();

        if (! $auth->isLoggedIn()) {
            return;
        }

        $this->view->nickName = $this->_getAccountInfos('nickname');

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

    /**
     * ログインしているユーザーの情報を取得する
     *
     * @param string[option] $columnName 取得するカラム　指定がない場合はすべて取得する
     * @return array ユーザーの情報
     * @author suzuki-mar
     */
    protected function _getAccountInfos($columnName = null)
    {
        $auth = new Admin_Model_Auth();

        if (is_null($columnName)) {
            $result = $auth->getAccountInfos();
        } else {
            $accountInfos = $auth->getAccountInfos();
            $result = $accountInfos[$columnName];
        }
        
        return $result;
    }
}
