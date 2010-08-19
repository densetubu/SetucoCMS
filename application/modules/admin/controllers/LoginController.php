<?php

/**
 * ログイン画面
 * 
 * @author Yuu Yamanaka
 */
class Admin_LoginController extends Zend_Controller_Action
{
    public function init()
    {
        // レイアウトを無効にする
        Zend_Layout::getMvcInstance()->disableLayout();
    }

    /**
     * ログインフォーム
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->view->errors = $this->_getParam('errors');
        $this->view->form = $this->_getParam('form', $this->_createLoginForm());
    }
    
    /**
     * ログイン処理
     * 
     * @return void
     */
    public function authAction()
    {
        $form = $this->_createLoginForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('form', $form);
            return $this->_forward('index');
        }
        
        $authModel = new Admin_Model_Auth();
        if (!$authModel->login($form->getValue('loginId'),
                               $form->getValue('password'))) {
            $this->_setParam('form', $form);
            $this->_setParam('errors',
                    array('ログインIDまたはパスワードが間違っています'));
            return $this->_forward('index');
        };

        $this->_redirect('/admin/index/index');        
    }

    /**
     * ログアウト処理
     * 
     * @return void
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/admin/login');        
    }
    
    /**
     * ログインフォームオブジェクトを作成して返す
     * 
     * @return Setuco_Form
     */
    private function _createLoginForm()
    {
        $form = new Setuco_Form();
        $form->setMethod('post');
        $form->setAction($this->_helper->url('auth'));
        
        $form->addElement('text', 'loginId', array(
            'label'    => 'アカウント名',
            'required' => true,
            'filters'  => array('StringTrim')
        ));
        $form->addElement('password', 'password', array(
            'label'    => 'パスワード',
            'required' => true
        ));
        $form->addElement('submit', 'submit', array(
            'label'    => 'ログイン'
        ));

        // デコレータの調整
        $form->setMinimalDecoratorElements('submit');

        return $form;
    }
}

