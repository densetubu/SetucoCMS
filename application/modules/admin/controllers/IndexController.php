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
        $this->view->ambition = $this->_ambitionService->findAmbition();
        $this->view->ambitionForm = $this->_getParam('ambitionForm',
                $this->_createAmbitionForm());

        // 更新状況
        $this->view->updateStatus = $this->_siteService->getUpdateStatus();

        // 最終更新日
        $lastUpdateInfo = $this->_siteService->getLastUpdateDateWithPastDays();
        if ($lastUpdateInfo !== false) {
            $this->view->lastUpdateDateString =
                $lastUpdateInfo['lastUpdateDate']->toString('YYYY/MM/dd')
                . '（' . $lastUpdateInfo['pastDays'] . '日経過）';
        } else {
            $this->view->lastUpdateDateString = '更新されたページがありません。';
        }

        // 今月の作成（公開）ページ数
        $createdPageCount = $this->_pageService->countPagesCreatedThisMonth();
        $this->view->createdPageCount = $createdPageCount;
        $this->view->diffGoal = $this->_convertDiffGoal2String(
                $createdPageCount - $this->_goalService->findGoalPageCountThisMonth());

        // 総ページ数
        $this->view->totalPageCount = $this->_pageService->countAllPages();

        // サイト開設日
        $siteDateInfo = $this->_siteService->getOpenDateWithPastDays();
        $this->view->openDate = $siteDateInfo['openDate']->toString('YYYY/MM/dd');
        $this->view->pastDaysFromOpen = $siteDateInfo['pastDays'];

        // 最近作ったページ
        $lastCreatedPages = $this->_pageService->findLastCreatedPages(5);
        $modifiedLastCreatedPages = array();
        foreach ($lastCreatedPages as $page) {
            $page['category_name'] = Setuco_Data_Converter_CategoryInfo::convertCategoryName4View($page['category_name']);
            $page['status'] = Setuco_Data_Converter_PageInfo::convertStatus2String($page['status']);
            $modifiedLastCreatedPages[] = $page;
        }
        $this->view->lastCreatedPages = $modifiedLastCreatedPages;
    }

    /**
     * 目標との差分ページ数を表示用の文言に変換します。
     *
     * @param  int $diffGoal 目標との差分ページ数
     * @return string 更新状況の文字列
     * @author charlesvineyard
     */
    private function _convertDiffGoal2String($diffGoal)
    {
        if (!is_int($diffGoal)) {
            throw new UnexpectedValueException('目標との差分ページ数が[' . $diffGoal . ']になっています。');
        }
        if (0 === $diffGoal) {
            return '目標達成！';
        }
        if (0 < $diffGoal) {
            return '目標からプラス' . $diffGoal . 'ページ！';
        }
        if (0 > $diffGoal) {
            return '目標まであと' . ($diffGoal * -1) . 'ページ！';
        }
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
        $preAmbition = $form->getValue('ambition');
        if (!$form->isValid($_POST)) {
            $form->getElement('ambition')->setValue($preAmbition);
            $this->_setParam('ambitionForm', $form);
            return $this->_forward('index');
        }
        $this->_ambitionService->updateAmbition($form->getValue('ambition'));
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
        $form->setAttrib('id', 'ambitionForm')
             ->setMethod('post')
             ->setAction($this->_helper->url('update-ambition'));
        $form->addElement('text', 'ambition', array(
            'id'         => 'ambition',
            'required'   => true,
            'value'      => $this->_ambitionService->findAmbition(),
            'filters'    => array('StringTrim'),
            'validators' => $this->_makeAmbitionValidators()
        ));
        $form->addElement('submit', 'submit', array(
            'id'    => 'sub',
            'label' => '保存'
        ));
        $form->addElement('button', 'cancel', array(
            'id'      => 'cancel',
            'label'   => 'キャンセル',
            'onclick' => 'hideAmbitionEdit()'
        ));
        $form->setMinimalDecoratorElements(array('ambition', 'submit', 'cancel'));
        return $form;
    }

    /**
     * 野望用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makeAmbitionValidators()
    {
        $validators[] = array();

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('野望を入力してください。');
        $validators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
            array(
                'max' => 100
            )
        );
        $stringLength->setMessage('野望は%max%文字以下で入力してください。');
        $stringLength->setEncoding("UTF-8");
        $validators[] = array($stringLength, true);

        return $validators;
    }

    /**
     * 目標を更新するフォームを表示するアクションです
     *
     * @return void
     * @author charlesvineyard
     */
    public function formGoalAction()
    {
        $this->view->goalForm = $this->_getParam('form', $this->_createGoalForm());

        // フラッシュメッセージ設定
        $this->_showFlashMessages();
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
        $form->addPrefixPath('Setuco_Form_Decorator_', 'Setuco/Form/Decorator/', 'decorator')
             ->setAttrib('id', 'goalForm')
             ->setMethod('post')
             ->setAction($this->_helper->url('update-goal'))
             ->setDecorators(array('FormElements', 'Form'));
        $goal = new Zend_Form_Element_Text('goal', array('label' => '一ヶ月の新規作成数'));
        $goalValue = $this->_goalService->findGoalPageCountThisMonth();
        $goal->setValue($goalValue)
             ->setAttrib('onblur', 'if(this.value == \'\') { this.value=\'' . $goalValue . '\'; }')
             ->addPrefixPath('Setuco_Filter', 'Setuco/Filter/', 'filter')
             ->setFilters(array('StringTrim', 'HalfSizeInt'))
             ->setRequired(true)
             ->addValidators($this->_makeGoalPageCountValidators())
             ->setDecorators(array(
                 'ViewHelper',
                 array('SuffixString', array('value' => 'ページ')),    // テキストボックスの後ろの文字列
                 array('HtmlTag', array('tag' => 'dd')),
                 array('Label', array('tag' => 'dt'))));
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('更新目標を変更')
               ->setDecorators(array(
                   'ViewHelper',
                   array('HtmlTag', array('tag' => 'p', 'class' => 'editAreaP'))));
        $form->addElements(array(
            $goal,
            $submit
        ));
        return $form;
    }

    /**
     * 更新目標の一ヶ月の新規作成数のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makeGoalPageCountValidators()
    {
        $validators[] = array();

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('一ヶ月の新規作成数を入力してください。');
        $validators[] = array($notEmpty, true);

        $int = new Zend_Validate_Int();
        $int->setMessage('一ヶ月の新規作成数は数字で入力してください。');
        $validators[] = array($int, true);

        $between  = new Zend_Validate_Between(
            array(
                'min' => 0,
                'max' => 999,
            )
        );
        $between->setMessage('一ヶ月の新規作成数は%min%以上%max%以下で入力してください。');
        $validators[] = array($between, true);

        return $validators;
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
