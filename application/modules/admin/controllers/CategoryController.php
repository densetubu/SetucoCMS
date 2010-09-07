<?php
/**
 * 管理側のカテゴリーページのコントローラー
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
 * @author      charlesvineyard suzuki-mar saniker10
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      charlesvineyard suzuki-mar saniker10
 */
class Admin_CategoryController extends Setuco_Controller_Action_Admin
{
    /** 
     * カテゴリーの新規作成するフォーム
     * カテゴリーの一覧表示のアクションです
     * 現在は、スタブからデータを取得している
     *
     * @return void
     * @author charlesvineyard suzuki-mar saniker10
     * @todo バリデートチェック
     */
    public function indexAction()
    {
        $service = new Admin_Model_Category();

        $this->view->categories = $service->getCategories();

        //編集用のフォームクラスを取得する
        $this->view->addForm = $this->_createAddForm();

        //新規作成用のフォームクラスを取得する
        $this->view->createForm = $this->_createCreateForm();

        //idがあったら、編集モードとする
        $isEdit = $this->_hasParam('id');

        //編集するカテゴリーが存在したらそのidを渡す
        if ($isEdit) {
            if ($service->isExistsId($this->_getParam('id'))) {
                $this->view->editId = $this->_getParam('id');
            }
        }

        //ページャーの設定をする
        $this->setPagerForView(50);
    }


    /** 
     * カテゴリーを新規作成するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     * @todo 内容の実装 現在はスケルトン
     */
    public function createAction()
    {
        $this->_redirect('/admin/category/index');        
    }

    /** 
     * カテゴリーを更新処理するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this->_redirect('/admin/category/index');        
    }

    /** 
     * カテゴリーを削除するアクションです
     *
     * @return void
     * @author charlesvineyard
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/category/index');        
    }

    /**
     * 新規作成用のフォームを作成する
     *
     * @return Zend_Form 新規作成用のフォーム
     * @author suzuki-mar
     */
    private function _createCreateForm()
    {
        //カテゴリー名を入力するinputタグを生成
        $inputItem =  new Zend_Form_Element_Text('cat_name');
        $inputItem->clearDecorators()
                     ->addDecorator('ViewHelper')
                     ->addDecorator('Label', array('tag' => null))
                     ->setAttrib('class', 'defaultInput')
                     ->setValue('新規カテゴリー')
                     ->setRequired()
                     ->addFilter('StringTrim')
                     ->addValidators(array(
                        array('NotEmpty', true),
                        array('stringLength', false, array(1, 100))
                        ));

        //submitボタンを生成する
        $submitItem = new Zend_Form_Element_Submit(
                'sub', array('class' => 'sub', 'label' => '追加'));

        $submitItem->clearDecorators()->addDecorator('ViewHelper');

        //フォームクラスに生成したタグを追加する
        $form = new Zend_Form();
        $form->addElement($inputItem);
        $form->addElement($submitItem);

        return $form;
    }


    /**
     * 編集用のフォームを作成する
     *
     * @return Zend_Form 編集用のフォーム
     * @author suzuki-mar
     */
    private function _createAddForm()
    {
        //カテゴリー名を入力するinputタグを生成
        $inputItem =  new Zend_Form_Element_Text('name');
        $inputItem->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null))
            ->setRequired()
            ->addFilter('StringTrim')
            ->addValidators(array(
                        array('NotEmpty', true),
                        array('stringLength', false, array(1, 100))
                        ));

        //idをセットするhiddenタグを生成
        $hiddenItem = new Zend_Form_Element_Hidden('id');
        $hiddenItem->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null))
            ->setRequired()
            ->addFilter('StringTrim')
            ->addValidators(array(
                        array('NotEmpty', true),
                        array('stringLength', false, array(1, 100)),
                        array('Int')
                        ));
 
            

        //submitボタンを生成する
        $submitItem = new Zend_Form_Element_Submit(
                'sub', array('class' => 'sub', 'label' => '保存'));

        $submitItem->clearDecorators()->addDecorator('ViewHelper');


        //フォームクラスの生成
        $form = new Zend_Form();

        $form->addElement($inputItem);
        $form->addElement($hiddenItem);
        $form->addElement($submitItem);
        return $form;

    }

}

