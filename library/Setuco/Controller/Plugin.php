<?php

/**
 * Setucoの標準コントローラプラグイン
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage Controller
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     Yuu Yamanaka, charlelsvineyard
 */

/**
 * @package    Setuco
 * @subpackage Controller
 * @author     Yuu Yamanaka, charlelsvineyard
 */
class Setuco_Controller_Plugin extends Zend_Controller_Plugin_Abstract
{
    /**
     * ディスパッチループの初期処理です。
     * 
     * @return void
     * @author Yuu Yamanaka, charlesvineyard
     */
    public function dispatchLoopStartup()
    {
        if ($this->getRequest()->getModuleName() == 'admin') {
            if ($this->_isLoginControllerRequested()) {
                return;
            }
            if (! $this->_isLoggedIn()) {
                $this->_redirectLogin();        
            }
            $this->_setNavigationEnable();
        }
    }
    
    /**
     * ログインコントローラにアクセスが来ているかを判断します。
     * 
     * @return boolean アクセスされていれば true
     * @author charlesvineyard
     */
    private function _isLoginControllerRequested()
    {
        return $this->getRequest()->getControllerName() == 'login';
    }
    
    /**
     * ログイン状態かどうかを判断します。
     * 
     * @return boolean ログイン状態なら true
     * @author charlesvineyard
     */
    private function _isLoggedIn()
    {
        return Zend_Auth::getInstance()->hasIdentity();
    }
    
    /**
     * ナビゲーションを有効にします。
     * 
     * @return void
     * @author charlesvineyard
     */
    private function _setNavigationEnable()
    {
        $actionStack = new Zend_Controller_Action_Helper_ActionStack();
        $actionStack->actionToStack('navigation', 'navigation');
    }
    
    /**
     * ログイン画面にリダイレクトします。
     * 
     * @return void
     * @author charlesvineyard
     */
    private function _redirectLogin()
    {
        $redirector = Zend_Controller_Action_HelperBroker::
                getStaticHelper('redirector');
        $redirector->goToSimple('index', 'login', 'admin');
    }

}