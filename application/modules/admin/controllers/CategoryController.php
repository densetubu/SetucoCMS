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
class Admin_CategoryController extends Setuco_Controller_Action_AdminAbstract {

    /**
     * 全アクションで使用するサービスクラス
     * @var Admin_Model_Category
     */
    private $_categoryService = null;

    /**
     * 新規登録用のバリデーションチェックフォーム
     *
     * @var Setuco_Form
     */
    private $_validateCreateForm = null;

    /**
     * 編集用のバリデーションチェックフォーム
     *
     * @var Setuco_Form
     */
    private $_validateUpdateForm = null;


    /**
     * コントローラーの共通設定をする
     * 全アクションで使用するサービスクラスのインスタンスをオブジェクト変数にする
     *
     * @reutn true
     * @author suzuki-mar
     */
    public function init() {
        //親クラスの設定を引き継ぐ
        parent::init();

        //全アクションで使用するサービスクラスのインスタンを生成する
        $this->_categoryService = new Admin_Model_Category();

        //新規作成用のバリデートフォーム
        $this->_validateCreateForm = $this->_validateCreate();

        //編集用のバリデートフォーム
        $this->_validateUpdateForm = $this->_validateUpdate();

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
    public function indexAction() {

        //idがあったら、編集モードとする
        $isEdit = $this->_hasParam('id');

        //編集するカテゴリーが存在したらそのidを渡す
        if ($isEdit) {
            if ($this->_categoryService->isExistsId($this->_getParam('id'))) {
                $this->view->editId = $this->_getParam('id');
            }
        }

        // フラッシュメッセージ設定
        $this->_showFlashMessages();

        //全部のデータからデータと該当したデータが何件あったか(limitしないで)を取得する
        $this->view->categories = $this->_categoryService->findCategories($this->_getParam('sort'), $this->_getPageNumber(), $this->_getPageLimit());
        $max = $this->_categoryService->countCategories();
        $this->setPagerForView($max);

        return true;
    }

    /**
     * カテゴリーを新規作成するアクションです
     * indexアクションに遷移します
     *
     * @return true
     * @author charlesvineyard suzuki-mar
     */
    public function createAction()
    {
        //フォームから値を送信されなかったら、indexに遷移する
        if (!$this->_request->isPost()) {
            $this->_redirect('/admin/category/index');
        }

        //新規登録のバリデートオブジェクトを取得する
        $validateForm = $this->_validateCreate();

        //入力したデータをバリデートチェックをする
        if ($this->_validateCreateForm->isValid($this->_getAllParams())) {
            $inputData = $this->_validateCreateForm->getValues();
            $registData['name'] = $inputData['cat_name'];

            //カテゴリーを新規作成する
            if ($this->_categoryService->registCategory($registData)) {
                $this->_helper->flashMessenger("「{$registData['name']}」を作成しました");
                $isSetFlashMessage = true;
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!isset($isSetFlashMessage)) {
            $this->_helper->flashMessenger($this->_getErrorMessage());
        }

        $this->_redirect('/admin/category/index');

        return true;
    }

    /**
     * カテゴリーを更新処理するアクションです
     * indexアクションに遷移します
     *
     * @return true
     * @author charlesvineyard suzuki_mar
     */
    public function updateAction() {
        //フォームから値を送信されなかったら、indexに遷移する
        if (!$this->_request->isPost()) {
            $this->_redirect('/admin/category/index');
        }

        //入力したデータをバリデートチェックをする
        if ($this->_validateUpdateForm->isValid($this->_getAllParams())) {
            $validateData  = $this->_validateUpdateForm->getValues();
            $updateData['name'] = $validateData['name'];

           $oldName = $this->_categoryService->findNameById($validateData['id']);

            //カテゴリーを編集する
            if ($this->_categoryService->updateCategory($validateData['id'], $updateData)) {

                //カテゴリー名が同じ場合は,違うメッセージを表示する
                if ($oldName === $updateData['name']) {
                    $actionMessage = "{$updateData['name']}のカテゴリーを編集しました。";
                } else {
                    $actionMessage = "{$oldName}から{$updateData['name']}にカテゴリー名を編集しました。";
                }

                $this->_helper->flashMessenger($actionMessage);
                $isSetFlashMessage = true;
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!isset($isSetFlashMessage)) {
            $this->_helper->flashMessenger($this->_getErrorMessage('update'));
        }


        $this->_redirect('/admin/category/index');
        return true;
    }

    /**
     * カテゴリーを削除するアクションです
     *
     * @return true
     * @author charlesvineyard suzuki-mar
     */
    public function deleteAction() {
        //フォームからidが送信されなかったら、indexに遷移する
        if (!$this->_hasParam('id')) {
            $this->_redirect('/admin/category/index');
        }

        //数値以外はエラー
        $validator = new Zend_Validate_Digits($this->_getParam('id'));
        if ($validator->isValid($this->_getParam('id'))) {

            $categoryName = $this->_categoryService->findNameById($this->_getParam('id'));

            //カテゴリーを削除する
            if ($this->_categoryService->deleteCategory($this->_getParam('id'))) {
                $this->_helper->flashMessenger("カテゴリー{$categoryName}の削除に成功しました");
                $isSetFlashMessage = true;
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!isset($isSetFlashMessage)) {
            $this->_helper->flashMessenger('カテゴリーの削除に失敗しました');
        }

        $this->_redirect('/admin/category/index');

        return true;
    }

    /**
     * 新規作成用のバリデートルールを作成する
     *
     *
     * @return Zend_Form 新規作成用のフォーム
     * @author suzuki-mar
     */
    private function _validateCreate() {
        //カテゴリー名を入力するinputタグを生成
        $form = new Setuco_Form();

        //inputタグだけのクラスを生成する
        $inputItem = $form->createElement('text', 'cat_name');
        //バリデートルールを設定する
        $inputItem = $this->_setValidateRuleOfName($inputItem);
        $form->addElement($inputItem);

        return $form;
    }

    /**
     * 編集用のバリデートオブジェクトを作成する
     *
     *
     * @return Zend_Form 編集用のフォーム
     * @author suzuki-mar
     */
    private function _validateUpdate() {
        //フォームクラスの生成
        $form = new Setuco_Form();


        //カテゴリー名を入力するinputタグを生成
        $inputItem = $form->createElement('text', 'name');
        //バリデートルールを設定する
        $inputItem = $this->_setValidateRuleOfName($inputItem, true);

        $form->addElement($inputItem);

        //idをセットするhiddenタグを生成
        $idItem = $form->createElement('hidden', 'id');
        //バリデートルールを設定する
        $idItem = $this->_setValidateRuleOfId($idItem);
        $form->addElement($idItem);

        //idをセットするhiddenタグを生成
        $parentIdItem = $form->createElement('hidden', 'parent_id');
        $parentIdItem = $this->_setValidateRuleOfParentId($parentIdItem);
        $form->addElement($parentIdItem);

        return $form;
    }

    /**
     * カテゴリー名のバリデートルールを設定する
     *
     * @param Zend_Form_Element $element バリデートルールを設定するElementインスタンス
     * @param boolean[option] $isUpdate 編集用のバリデートルールか デフォルトは新規登録
     * @author suzuki-mar
     */
    private function _setValidateRuleOfName(Zend_Form_Element $element, $isUpdate = false) {
        //編集と新規登録では、ルールを変更する
        if ($isUpdate) {
            $noRecordExistsOption = array('table' => 'category', 'field' => 'name', 'exclude' => array('field' => 'id', 'value' => $this->_getParam('id')));
        } else {
            $noRecordExistsOption = array('table' => 'category', 'field' => 'name');
        }

        $element->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
                    //文字列の長さを指定する
                    array('stringLength', true, array(1, 100)),
                    //同じカテゴリーは登録できないようにする
                    array('Db_NoRecordExists', true, $noRecordExistsOption
                        )));

        return $element;
    }

    /**
     * IDのバリデートルールを設定する
     *
     * @param Zend_Form_Element $element バリデートルールを設定するElementインスタンス
     * @author suzuki-mar
     */
    private function _setValidateRuleOfId(Zend_Form_Element $element) {
        $element->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
                    array('stringLength', false, array(1, 100)),
                    array('Int')
                ));

        return $element;
    }

    /**
     * parent_idのバリデートルールを設定する
     *
     * @param Zend_Form_Element $element バリデートルールを設定するElementインスタンス
     * @author suzuki-mar
     */
    private function _setValidateRuleOfParentId(Zend_Form_Element $element) {
        $element->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
                    array('stringLength', false, array(1, 100)),
                    array('Int')
                ));

        return $element;
    }

    /**
     * バリデートエラーメッセージを取得する
     *
     * @param  String[option] $validateType createだと新規作成 updateだと編集　デフォルトは、create
     * @return String バリデートエラーメッセージ
     * @author suzuki-mar
     */
    private function _getErrorMessage($validateType = 'create')
    {

        if ($validateType === 'create') {
           $errors = $this->_validateCreateForm->getMessages('cat_name');
        } else {
           $errors = $this->_validateUpdateForm->getMessages('name');
        }

            //なんのメッセージが来るかわからないが、エラーメッセージは一つなので
            foreach ($errors as $key => $value) {
              $errorMessage = $value;
              //文字を入力しなかった場合
              if ($key === 'isEmpty') {
                  $errorMessage = 'カテゴリー名を入力してください。';
              }
            }

            //不正なエラー IDを文字列にするとか
            if (!isset($errorMessage)) {
                $errorMessage = 'カテゴリーの新規作成に失敗しました';
            }

       return $errorMessage;
    }
}

