<?php

/**
 * せつこの標準コントローラプラグイン
 * 
 * @author Yuu Yamanaka
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
            if ($this->_isLoginControllerRequired()) {
                return;
            }
            if ($this->_isLoggedIn()) {
                $this->_setNavigationEnable();
                return;
            }
            $this->_redirectLogin();        
        }
    }
    
    /**
     * ログインコントローラにアクセスが来ているかを判断します。
     * 
     * @return boolean アクセスされていれば true
     * @author charlesvineyard
     */
    private function _isLoginControllerRequired()
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