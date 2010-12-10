<?php
/**
 * タグ管理のコントローラ
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
 * タグ管理のコントローラ
 *
 * @package    Admin
 * @subpackage Controller
 * @author     charlesvineyard
 */
class Admin_TagController extends Setuco_Controller_Action_AdminAbstract
{
    /**
     * タグサービス
     *
     * @var Admin_Model_Tag
     */
    private $_tagService;

    /**
     * 初期処理
     *
     * @author charlesvineyard
     */
    public function init()
    {
        parent::init();
        $this->_tagService = new Admin_Model_Tag();
    }

    /**
     * タグの新規作成するフォーム
     * タグの一覧表示のアクションです
     *
     * @return void
     * @author charlesvineyard
     */
    public function indexAction()
    {
        $this->view->newTagForm = $this->_getParam('newTagForm', $this->_createNewTagForm());
        $this->view->editTagForm = $this->_getParam('editTagForm', $this->_createEditTagForm());
        $this->view->tags = $this->_tagService->findTags($this->_getParam('order', 'asc'), $this->_getPageNumber(), $this->_getPageLimit());
        $this->setPagerForView($this->_tagService->countAllTags());
        $this->_showFlashMessages();
    }

    /**
     * タグの新規作成フォームを作成します。
     *
     * @return Setuco_Form タグ作成フォーム
     * @author charlesvineyard
     */
    private function _createNewTagForm()
    {
        $form = new Setuco_Form();
        $form->clearDecorators()
             ->setDisableLoadDefaultDecorators(true)
             ->setAttrib('id', 'newTagForm')
             ->setMethod('post')
             ->setAction($this->_helper->url('create'))
             ->addDecorator('FormElements')
             ->addDecorator('HtmlTag', array('tag' => 'p', 'class' => 'default'))
             ->addDecorator('Form');
        $tag = new Zend_Form_Element_Text('tag', array(
            'id'         => 'new_tag',
            'class'      => 'defaultInput',
            'value'      => '新規タグ',
            'required'   => true,
            'validators' => $this->_makeTagValidators(),
            'filters'    => array('StringTrim')
        ));
        $submit = new Zend_Form_Element_Submit('sub', array(
            'id'    => 'sub_create',
            'label' => '追加'
        ));
        $form->addElements(array($tag, $submit))
             ->setMinimalDecoratorElements(array('tag', 'sub'));
        return $form;
    }

    /**
     * タグの編集フォームを作成します。
     *
     * @return Setuco_Form タグ編集フォーム
     * @author charlesvineyard
     */
    private function _createEditTagForm()
    {
        $form = new Setuco_Form();
        $form->clearDecorators()
             ->setDisableLoadDefaultDecorators(true)
             ->setAttrib('id', 'editTagForm')
             ->setMethod('post')
             ->setAction($this->_helper->url('update'))
             ->addDecorator('FormElements')
             ->addDecorator('HtmlTag', array('tag' => 'p'))
             ->addDecorator('Form');
        $tag = new Zend_Form_Element_Text('tag', array(
            'id'         => 'tag',
            'value'      => '',
            'required'   => true,
            'validators' => $this->_makeTagValidators(),
            'filters'    => array('StringTrim')
        ));
        $submit = new Zend_Form_Element_Submit('sub', array(
            'id'    => 'sub',
            'label' => '保存'
        ));
        $cancel = new Zend_Form_Element_Button('cancel', array(
            'id'      => 'cancel',
            'label'   => 'キャンセル',
            'onclick' => 'hideTagEdit(this)'
        ));
        $id = new Zend_Form_Element_Hidden('id', array(
            'id'    => 'id',
            'value' => ''  // ビューでセットする
        ));
        $preTag = new Zend_Form_Element_Hidden('preTag', array(
            'id'    => 'preTag',
            'value' => ''  // ビューでセットする
        ));
        $form->addElements(array($tag, $submit, $cancel, $id, $preTag))
             ->setMinimalDecoratorElements(array('tag', 'sub', 'cancel', 'id', 'preTag'));
        return $form;
    }

    /**
     * タグ名用のバリデーターを作成する。
     *
     * @return array Zend_Validateインターフェースとオプションの配列の配列
     * @author charlesvineyard
     */
    private function _makeTagValidators()
    {
        $validators[] = array();

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('タグ名を入力してください。');
        $validators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
            array(
                'min' => 2,
                'max' => 50
            )
        );
        $stringLength->setMessage('タグ名は%min%文字以上%max%文字以下で入力してください。');
        $stringLength->setEncoding("UTF-8");
        $validators[] = array($stringLength, true);

        $noRecordExists = new Zend_Validate_Db_NoRecordExists(
            array(
                'table' => 'tag',
                'field' => 'name'
            )
        );
        $noRecordExists->setMessage('「%value%」は既に登録されています。');
        $validators[] = array($noRecordExists, true);

        return $validators;
    }

    /**
     * タグを新規作成するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     */
    public function createAction()
    {
        $form = $this->_createNewTagForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('newTagForm', $form);
            return $this->_forward('index');
        }
        $this->_tagService->registTag($form->getValue('tag'));
        $this->_helper->flashMessenger('新規タグを作成しました。');
        $this->_helper->redirector('index');
    }

    /**
     * タグを更新するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     */
    public function updateAction()
    {
        $form = $this->_createEditTagForm();
        if (!$form->isValid($_POST)) {
            $this->_setParam('editTagForm', $form);
            return $this->_forward('index');
        }
        $this->_tagService->updateTag($form->getValue('id'), $form->getValue('tag'));
        $this->_helper->flashMessenger('「' . $form->getValue('preTag') . '」を「' . $form->getValue('tag') . '」に変更しました。');
        $this->_helper->redirector('index');
    }

    /**
     * タグを削除するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $validator = new Zend_Validate_Digits();
        if (!$validator->isValid($id)) {
            $this->_helper->redirector('index');
        }
        $tag = $this->_tagService->findTag($id);
        $this->_tagService->deleteTag($id);
        $this->_helper->flashMessenger('「' . $tag['name'] . '」を削除しました。');
        $this->_helper->redirector('index');
    }

}