<?php
/**
 * 管理側のカテゴリーページのコントローラー
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category 	Setuco
 * @package 	Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project. (http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      charlesvineyard suzuki-mar saniker10
 */


/**
 * @package     Admin
 * @subpackage  Controller
 * @author      charlesvineyard suzuki-mar saniker10
 */
class Admin_CategoryController extends Setuco_Controller_Action_Admin_Abstract
{
    /**
     * 全アクションで使用するサービスクラス
     * @var Admin_Model_Category
     */
    private $_service = null;

    /**
     * コントローラーの共通設定をする
     * 全アクションで使用するサービスクラスのインスタンスをオブジェクト変数にする 
     *
     * @reutn true
     * @author suzuki-mar
     */
    public function init() 
    {
        //親クラスの設定を引き継ぐ
        parent::init();

        //全アクションで使用するサービスクラスのインスタンを生成する
        $this->_service = new Admin_Model_Category();

        return true;
    }


    /** 
     * カテゴリーの新規作成するフォーム
     * カテゴリーの一覧表示のアクションです
     * 現在は、スタブからデータを取得している
     *
     * @return true
     * @author charlesvineyard suzuki-mar saniker10
     * @todo Flashメッセージの取得
     */
    public function indexAction()
    {

        //編集用のフォームクラスを取得する
        $this->view->updateForm = $this->_createUpdateForm();

        //新規作成用のフォームクラスを取得する
        $this->view->createForm = $this->_createCreateForm();

        //idがあったら、編集モードとする
        $isEdit = $this->_hasParam('id');

        //編集するカテゴリーが存在したらそのidを渡す
        if ($isEdit) {
            if ($this->_service->isExistsId($this->_getParam('id'))) {
                $this->view->editId = $this->_getParam('id');
            }
        }

        //フラッシュメッセージがある場合のみ設定する
        if ($this->_helper->flashMessenger->hasMessages()){
            $flashMessages = $this->_helper->flashMessenger->getMessages();
            $this->view->flashMessage = $flashMessages[0];
        }


        //全部のデータからデータと該当したデータが何件あったか(limitしないで)を取得する
        $this->view->categories = $this->_service->searchCategories($this->_getParam('sort'), $this->_getPage(), parent::PAGE_LIMIT);
        $max = $this->_service->countCategories();
        $this->setPagerForView($max);

        return true;
    }


    /** 
     * カテゴリーを新規作成するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     */
    public function createAction()
    {
        //バリデートするFormオブジェクトを取得する
        $validateForm = $this->_createCreateForm();


        //入力したデータをバリデートチェックをする
        if ($validateForm->isValid($this->_getAllParams() ) ) {
            
            //カテゴリーを新規作成する
            if ($this->_service->registCategory($this->_getAllParams())) {
                $this->_helper->flashMessenger('カテゴリーの新規作成に成功しました');
                $isSetFlashMessage = true;
            }
        } 

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!isset($isSetFlashMessage)) {
            $this->_helper->flashMessenger('カテゴリーの新規作成に失敗しました');
        } 
    
        $this->_redirect('/admin/category/index');        
    }

    /** 
     * カテゴリーを更新処理するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     */
    public function updateAction()
    {
        //バリデートするFormオブジェクトを取得する
        $validateForm = $this->_createUpdateForm();

        //入力したデータをバリデートチェックをする
        if ($validateForm->isValid($this->_getAllParams()) ) {
            
            //カテゴリーを編集する
            if ($this->_service->updateCategory($this->_getAllParams(), $this->_getParam('id')) ) {
                $this->_helper->flashMessenger('カテゴリーの編集に成功しました');
                $isSetFlashMessage = true;
            }
        } 

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!isset($isSetFlashMessage)) {
            $this->_helper->flashMessenger('カテゴリーの編集に失敗しました');
        } 
    
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

        //数値以外はエラー
        $validator = new Zend_Validate_Digits($this->_getParam('id'));
        if ($validator->isValid($this->_getParam('id'))) {

             //カテゴリーを削除する
            if ($this->_service->deleteCategory($this->_getParam('id')) ) {
                $this->_helper->flashMessenger('カテゴリーの削除に成功しました');
                $isSetFlashMessage = true;
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!isset($isSetFlashMessage)) {
            $this->_helper->flashMessenger('カテゴリーの削除に失敗しました');
        } 

        $this->_redirect('/admin/category/index');        
    }

    /**
     * 新規作成用のフォームを作成する
     * class属性などは、view側で指定する
     *
     * @return Zend_Form 新規作成用のフォーム
     * @author suzuki-mar
     */
    private function _createCreateForm()
    {
        //カテゴリー名を入力するinputタグを生成
        $form = new Setuco_Form();

        //inputタグだけのクラスを生成する
        $inputItem =  $form->createElementOfViewHelper('text', 'cat_name');
        $inputItem->setRequired()
                     ->addFilter('StringTrim')
                     ->addValidators(array(
                        array('NotEmpty', true),
                        array('stringLength', false, array(1, 100))
                        ));
        $form->addElement($inputItem);

        //submitボタンを生成する
        $submitItem = $form->createElementOfViewHelper('submit', 'sub');
        $form->addElement($submitItem);

        return $form;
    }


    /**
     * 編集用のフォームを作成する
     * class属性などは、view側で指定する
     *
     * @return Zend_Form 編集用のフォーム
     * @author suzuki-mar
     */
    private function _createUpdateForm()
    {
        //フォームクラスの生成
        $form = new Setuco_Form();

        //カテゴリー名を入力するinputタグを生成
        $inputItem =  $form->createElementOfViewHelper('text', 'name');
        $inputItem->setRequired()
            ->addFilter('StringTrim')
            ->addValidators(array(
                        array('NotEmpty', true),
                        array('stringLength', false, array(1, 100))
                        ));
        $form->addElement($inputItem);

        //idをセットするhiddenタグを生成
        $hiddenItem = $form->createElementOfViewHelper('hidden', 'id');
        $hiddenItem->setRequired()
            ->addFilter('StringTrim')
            ->addValidators(array(
                        array('NotEmpty', true),
                        array('stringLength', false, array(1, 100)),
                        array('Int')
                        ));
        $form->addElement($hiddenItem);

        //submitボタンを生成する
        $submitItem = $form->createElementOfViewHelper('submit', 'sub');
        $form->addElement($submitItem);

        return $form;
    }

}

