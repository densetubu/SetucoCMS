<?php
/**
 * 管理側のTOPページのコントローラーです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category 	Setuco
 * @package 	Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author     
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author 
 */
class Admin_IndexController extends Setuco_Controller_Action_Admin
{
    /** 
     * トップページのアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {
        $ambition = new Admin_Model_Ambition();
        $this->view->ambition = $ambition->load();
        
        $this->view->ambitionForm = $this->_getParam('ambitionForm',
                $this->_createAmbitionForm());
    }

    /** 
     * 更新処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */ 
    public function updateAmbitionAction()
    {
        $form = $this->_createAmbitionForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('ambitionForm', $form);
            return $this->_forward('index');
        }
        $ambition = new Admin_Model_Ambition();
        $ambition->update($form->getValue('ambition'));
        
        $this->_redirect('/admin');
    }
    
    /**
     * 
     * @return void
     * @author charlesvineyard
     */
    private function _createAmbitionForm()
    {
        $form = new Setuco_Form();
        $form->setAttrib('id', 'ambitionForm');
        $form->setMethod('post');
        $form->setAction($this->_helper->url('update-ambition'));

        $form->addElement('text', 'ambition', array(
            'required' => true,
            'filters'  => array('StringTrim')
        ));
        $form->addElement('submit', 'submit', array(
            'label'    => '保存'
        ));
        $form->addElement('button', 'cancel', array(
            'label'    => 'キャンセル',
            'onclick'  => 'hideAmbitionForm()'
        ));
        // デコレータの調整
        $form->setMinimalDecoratorElements(array('ambition', 'submit', 'cancel'));

        return $form;
    }

    /** 
     * 野望を更新するフォームを表示するアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function formGoalAction()
    {
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages[0];
        }
    }

    /** 
     * 野望を更新するアクションです 
     * formGoalアクションに遷移します 
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateGoalAction()
    {
        $this->_helper->flashMessenger('更新目標を変更しました。');
        $this->_redirect('/admin/index/form-goal');
    }

}

