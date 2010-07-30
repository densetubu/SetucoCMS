<?php

class Admin_LoginController extends Zend_Controller_Action
{

    public function init()
    {
        // レイアウトを無効にする
        $mvcInstance = Zend_Layout::getMvcInstance();
        $mvcInstance->disableLayout();
    }

    public function indexAction()
    {
        
    }
    
    public function authAction()
    {
        $this->_redirect('/admin/index/index');        
    }

    public function logoutAction()
    {
        $this->_redirect('/admin/login/index');        
    }
}

