<?php

/**
 * せつこの標準コントローラプラグイン
 * 
 * @author Yuu Yamanaka
 */
class Setuco_Controller_Plugin extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup()
    {
        if ($this->getRequest()->getModuleName() == 'admin') {
            $this->_checkAdminLogin();
        }
    }
    
    /**
     * 管理画面におけるログイン状態をチェックする
     * 
     * @return void
     */
    private function _checkAdminLogin()
    {
        if ($this->getRequest()->getControllerName() == 'login') {
            return;
        }
        
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->getResponse()->setRedirect('/admin/login');
            return;
        }
    }
}