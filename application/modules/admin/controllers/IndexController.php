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
     * 野望サービス
     *
     * @var Admin_Model_Ambition
     */
    private $_ambition;

    /**
     * サイトサービス
     *
     * @var Admin_Model_Site
     */
    private $_site;

    /**
     * 更新目標サービス
     *
     * @var Admin_Model_Goal
     */
    private $_goal;

    /**
     * ページサービス
     *
     * @var Admin_Model_Page
     */
    private $_page;

    /**
     * 初期処理
     *
     * @author charlesvineyard
     */
    public function init()
    {
        parent::init();
        $this->_ambition = new Admin_Model_Ambition();
        $this->_site = new Admin_Model_Site();
        $this->_goal = new Admin_Model_Goal();
        $this->_page = new Admin_Model_Page();
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
        $this->view->ambition = $this->_ambition->load();
        $this->view->ambitionForm = $this->_getParam('ambitionForm',
                $this->_createAmbitionForm());

        // 更新状況
        $updateStatus = $this->_site->getUpdateStatus();
        $updateStatusStr = Setuco_Data_Converter_UpdateStatus::convertUpdateStatus2String($updateStatus);
        $this->view->updateStatus = $updateStatusStr;

        // 最終更新日
        $lastUpdateInfo = $this->_site->getLastUpdateDateWithPastDays();
        $this->view->lastUpdateDate = $lastUpdateInfo['lastUpdateDate']->toString('YYYY/MM/dd', 'ja_JP');
        $this->view->pastDaysFromLastUpdate = $lastUpdateInfo['pastDays'];

        // 今月の作成（公開）ページ数
        $createdPageCount = $this->_findCreatedPageCount();
        $this->view->createdPageCount = $createdPageCount;
        $this->view->diffGoal = Setuco_Util_String::convertSign2String($this->_goal->loadGoalPageCount() - $createdPageCount);

        // 総ページ数
        $this->view->totalPageCount = $this->_page->countPage();

        // サイト開設日
        $siteDateInfo = $this->_site->getOpenDateWithPastDays();
        $this->view->openDate = $siteDateInfo['openDate']->toString('YYYY/MM/dd', 'ja_JP');
        $this->view->pastDaysFromOpen = $siteDateInfo['pastDays'];

        // 最近作ったページ
        $lastCreatedPages = $this->_page->loadLastCreatedPages(5);
        $modifiedLastCreatedPages = array();
        foreach ($lastCreatedPages as $page) {
            $page['status'] = Setuco_Data_Converter_PageInfo::convertStatus2String($page['status']);
            $modifiedLastCreatedPages[] = $page;
        }
        $this->view->lastCreatedPages = $modifiedLastCreatedPages;
    }

    /**
     * 今月作成（公開）したページ数を取得します。
     *
     * @return int ページ数
     * @author charlesvineyard
     */
    private function _findCreatedPageCount()
    {
        $date = new Zend_Date();
        return $this->_page->countPage(
                Setuco_Data_Constant_Page::STATUS_RELEASE,
                $date->get('YYYY', 'ja_JP'),
                $date->get('MM', 'ja_JP')
        );
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
        $this->_ambition->update($form->getValue('ambition'));

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
        $form =  $this->_createGoalForm();
        $form->getElement('goal')
             ->setValue($this->_goal->loadGoalPageCount());
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
        $this->_goal->updateGoalPageCount();
        $this->_helper->flashMessenger('更新目標を変更しました。');
        $this->_redirect('/admin/index/form-goal');
    }

}