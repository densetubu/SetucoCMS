<?php
/**
 * 管理側のTOPページのコントローラ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * 管理側のTOPページのコントローラ
 *
 * @package    Admin
 * @subpackage Controller
 * @author     charlesvineyard
 */
class Admin_IndexController extends Setuco_Controller_Action_Admin
{
    /** 
     * トップページのアクションです
     *
     * @return void
     * @author charlesvineyard
     */
    public function indexAction()
    {
        $ambition = new Admin_Model_Ambition();
        $this->view->ambition = $ambition->load();
        
        $this->view->ambitionForm = $this->_getParam('ambitionForm',
                $this->_createAmbitionForm());
    }

    /** 
     * 野望の更新アクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
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
     * 野望のフォームを作成します。
     * 
     * @return Setuco_Form 野望フォーム
     * @author charlesvineyard
     */
    private function _createAmbitionForm()
    {
        $form = new Setuco_Form();
        $form->setAttrib('id', 'ambitionForm');
        $form->setMethod('post');
        $form->setAction($this->_helper->url('update-ambition'));
        $form->getDecorator('HtmlTag')->clearOptions();

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
        
        // 付属タグの除去
        $form->setMinimalDecoratorElements(array('ambition', 'submit', 'cancel', 'ambitionForm'));

        return $form;
    }

    /** 
     * 目標を更新するフォームを表示するアクションです
     *
     * @return void
     * @author charlesvineyard
     */
    public function formGoalAction()
    {
        $goal = new Admin_Model_Goal();

        $this->view->goalForm = $this->findGoalForm();
        
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages[0];
        }
    }

    /**
     * 目標更新フォームを取得します。
     * 
     * @return Setuco_Form
     * @author charlesvineyard
     */
    private function findGoalForm()
    {
        if ($this->_hasParam('form')) {
            return $this->_getParam('form');
        }
        $goal = new Admin_Model_Goal();
        $form =  $this->_createGoalForm();
        $form->getElement('goal')
             ->setValue($goal->loadGoalPageCount());
        return $form; 
    }
    
    /**
     * 目標更新のフォームを作成します。
     * 
     * @return Setuco_Form 目標更新フォーム
     * @author charlesvineyard
     */
    private function _createGoalForm()
    {
        $form = new Setuco_Form();
        $form->setAttrib('id', 'goalForm');
        $form->setMethod('post');
        $form->setAction($this->_helper->url('update-goal'));
        $form->getDecorator('HtmlTag')->setOption('class', 'editArea');

        $form->addElement('text', 'goal', array(
            'label'      => '一ヶ月の新規作成数',
            'validators' => array('int'),
            'required' => true,
            'filters'  => array('StringTrim')
        ));
        $form->addElement('submit', 'submit', array(
                    'label'    => '更新目標を変更'
        ));        
        // 付属タグの除去
        $form->setMinimalDecoratorElements(array('goalForm', 'submit'));

        return $form;
    }
    

    /** 
     * 目標を更新するアクションです 
     * formGoalアクションに遷移します 
     *
     * @return void
     * @author charlesvineyard
     */
    public function updateGoalAction()
    {
        $form = $this->_createGoalForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('form', $form);
            return $this->_forward('form-goal');
        }
        
        
        $goal = new Admin_Model_Goal();
        $goal->updateGoalPageCount();
        $this->_helper->flashMessenger('更新目標を変更しました。');
        $this->_redirect('/admin/index/form-goal');
    }

}