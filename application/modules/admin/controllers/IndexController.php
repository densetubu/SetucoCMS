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
class Admin_IndexController extends Setuco_Controller_Action_AdminAbstract
{
    /**
     * 野望サービス
     *
     * @var Admin_Model_Ambition
     */
    private $_ambitionService;

    /**
     * サイトサービス
     *
     * @var Admin_Model_Site
     */
    private $_siteService;

    /**
     * 更新目標サービス
     *
     * @var Admin_Model_Goal
     */
    private $_goalService;

    /**
     * ページサービス
     *
     * @var Admin_Model_Page
     */
    private $_pageService;

    /**
     * 初期処理
     *
     * @author charlesvineyard
     */
    public function init()
    {
        parent::init();
        $this->_ambitionService = new Admin_Model_Ambition();
        $this->_siteService = new Admin_Model_Site();
        $this->_goalService = new Admin_Model_Goal();
        $this->_pageService = new Admin_Model_Page();
    }


    /**
     * トップページのアクションです
     *
     * @return void
     * @author charlesvineyard
     */
    public function indexAction()
    {
        // 野望
        $this->view->ambitionForm = $this->_getParam('ambitionForm',
                $this->_createAmbitionForm());

        // 更新状況
        $this->view->updateStatus = $this->_siteService->getUpdateStatus();

        // 最終更新日
        $lastUpdateInfo = $this->_siteService->getLastUpdateDateWithPastDays();
        $this->view->lastUpdateDate = $lastUpdateInfo['lastUpdateDate']->toString('YYYY/MM/dd', 'ja_JP');
        $this->view->pastDaysFromLastUpdate = $lastUpdateInfo['pastDays'];

        // 今月の作成（公開）ページ数
        $createdPageCount = $this->_pageService->countPagesCreatedThisMonth();
        $this->view->createdPageCount = $createdPageCount;
        $this->view->diffGoal = Setuco_Data_Converter_UpdateStatus::convertDiffGoal2String(
                $createdPageCount - $this->_goalService->loadGoalPageCountThisMonth());

        // 総ページ数
        $this->view->totalPageCount = $this->_pageService->countPages();

        // サイト開設日
        $siteDateInfo = $this->_siteService->getOpenDateWithPastDays();
        $this->view->openDate = $siteDateInfo['openDate']->toString('YYYY/MM/dd', 'ja_JP');
        $this->view->pastDaysFromOpen = $siteDateInfo['pastDays'];

        // 最近作ったページ
        $lastCreatedPages = $this->_pageService->loadLastCreatedPages(5);
        $modifiedLastCreatedPages = array();
        foreach ($lastCreatedPages as $page) {
            $page['status'] = Setuco_Data_Converter_PageInfo::convertStatus2String($page['status']);
            $modifiedLastCreatedPages[] = $page;
        }
        $this->view->lastCreatedPages = $modifiedLastCreatedPages;
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
        $this->_ambitionService->update($form->getValue('ambition'));
        $this->_helper->redirector('index');
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
        $form->clearDecorators()
             ->setDisableLoadDefaultDecorators(true)
             ->setAttrib('id', 'ambitionForm')
             ->setMethod('post')
             ->setAction($this->_helper->url('update-ambition'));
        $form->addElement('text', 'ambition', array(
            'id'      => 'ambition',
            'name'    => 'ambition',
            'value'   => $this->_ambitionService->load(),
            'filters' => array('StringTrim')
        ));
        $form->addElement('submit', 'submit', array(
            'id'    => 'sub',
            'name'  => 'sub',
            'label' => '保存'
        ));
        $form->addElement('button', 'cancel', array(
            'id'      => 'cancel',
            'name'    => 'cancel',
            'label'   => 'キャンセル',
            'onclick' => 'hideAmbitionEdit()'
        ));
        $form->setMinimalDecoratorElements(array('ambition', 'submit', 'cancel'));
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
        $this->view->goalForm = $this->_findGoalForm();
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
    private function _findGoalForm()
    {
        // validation でエラーになったとき
        if ($this->_hasParam('form')) {
            return $this->_getParam('form');
        }
        return $this->_createGoalForm();
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
        $form->clearDecorators()
             ->setDisableLoadDefaultDecorators(true)
             ->addPrefixPath('Setuco_Form_Decorator_', 'Setuco/Form/Decorator/', 'decorator')
             ->setAttrib('id', 'goalForm')
             ->setMethod('post')
             ->setAction($this->_helper->url('update-goal'))
             ->addDecorator('FormElements')
             ->addDecorator('Form');
        $goal = new Zend_Form_Element_Text('goal', array('label' => '一ヶ月の新規作成数'));
        $goalValue = $this->_goalService->loadGoalPageCountThisMonth();
        $goal->setValue($goalValue)
             ->setAttrib('onblur', 'if(this.value == \'\') { this.value=\'' . $goalValue . '\'; }')
             ->setRequired(true)
             ->setValidators(array('int'))
             ->setFilters(array('StringTrim'))
             ->clearDecorators()
             ->setDisableLoadDefaultDecorators(true)
             ->addDecorator('ViewHelper')
             ->addDecorator('SuffixString', array('value' => 'ページ'))    // テキストボックスの後ろの文字列
             ->addDecorator('Errors')
             ->addDecorator('HtmlTag', array('tag' => 'dd'))
             ->addDecorator('Label', array('tag' => 'dt'));
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('更新目標を変更')
               ->clearDecorators()
               ->setDisableLoadDefaultDecorators(true)
               ->addDecorator('ViewHelper')
               ->addDecorator('HtmlTag', array('tag' => 'p', 'class' => 'editAreaP'));
        $form->addElements(array(
            $goal,
            $submit
        ));
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
        $this->_goalService->updateGoalPageCountThisMonth($form->getElement('goal')->getValue());
        $this->_helper->flashMessenger('更新目標を変更しました。');
        $this->_helper->redirector('form-goal', 'index');
    }

}
