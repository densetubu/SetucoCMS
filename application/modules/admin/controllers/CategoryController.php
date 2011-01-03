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
class Admin_CategoryController extends Setuco_Controller_Action_AdminAbstract
{

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
     * 削除用のフォーム (エラーメッセージ）を入れるだけ
     *
     * @var Setuco_Form
     */
    private $_validateDeleteForm = null;

    /**
     * コントローラーの共通設定をする
     * 全アクションで使用するサービスクラスのインスタンスをオブジェクト変数にする
     *
     * @return void
     * @author suzuki-mar
     */
    public function init()
    {
        //親クラスの設定を引き継ぐ
        parent::init();

        //全アクションで使用するサービスクラスのインスタンを生成する
        $this->_categoryService = new Admin_Model_Category();

        //新規作成用のバリデートフォーム
        $this->_validateCreateForm = $this->_createNewValidateForm();
        //編集用のバリデートフォーム
        $this->_validateUpdateForm = $this->_createUpdateValidateForm();

        //削除用のフォーム（エラーメッセージを入れるだけ)
        $this->_validateDeleteForm = new Setuco_Form();
    }

    /**
     * カテゴリーの新規作成するフォーム
     * カテゴリーの一覧表示のアクションです
     * 現在は、スタブからデータを取得している
     *
     * @return void
     * @author charlesvineyard suzuki-mar saniker10
     * @todo Flashメッセージの取得
     */
    public function indexAction()
    {
        // フラッシュメッセージ設定
        $this->_showFlashMessages();

        //全部のデータからデータと該当したデータが何件あったか(limitしないで)を取得する
        $this->view->categories = $this->_categoryService->findCategories($this->_getParam('sort'), $this->_getPageNumber(), $this->_getPageLimit());
        $max = $this->_categoryService->countCategories();
        $this->setPagerForView($max);

        //バリデートに失敗したエラーフォームがあればセットする
        if ($this->_hasParam('errorForm')) {
            $this->view->errorForm = $this->_getParam('errorForm');
        }

        $this->view->inputCreateCategoryName = $this->_getParam('inputCreateCategoryName', $this->_getParam('inputCreateCategoryName', '新規カテゴリー'));
    }

    /**
     * カテゴリーを新規作成するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard suzuki-mar
     */
    public function createAction()
    {
        //フォームから値を送信されなかったら、indexに遷移する
        if (!$this->_request->isPost()) {
            $this->_redirect('/admin/category/index');
        }

        //入力したデータをバリデートチェックをする
        if ($this->_validateCreateForm->isValid($this->_getAllParams())) {
            $inputData = $this->_validateCreateForm->getValues();
            $registData['name'] = $inputData['cat_name'];

            $isCreateSuccess = $this->_categoryService->registCategory($registData);

            //カテゴリーを新規作成する
            if ($isCreateSuccess) {
                $this->_helper->flashMessenger("「{$registData['name']}」を作成しました");
            } else {
                $errorMessages = $this->_setExceptionErrorMessages('create');
            }
        }

        if (!(isset($isCreateSuccess) && $isCreateSuccess === true)) {
            $this->_setParam('errorForm', $this->_validateCreateForm);
            $this->_setParam('inputCreateCategoryName', $this->_getParam('cat_name'));
            return $this->_forward('index');
        }

        $this->_redirect('/admin/category/index');
    }

    /**
     * カテゴリーを更新処理するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard suzuki_mar
     */
    public function updateAction()
    {
        //フォームから値を送信されなかったら、indexに遷移する 直接アクセスの禁止
        if (!$this->_request->isPost()) {
            $this->_redirect('/admin/category/index');
        }

        //入力したデータをバリデートチェックをする
        if ($this->_validateUpdateForm->isValid($this->_getAllParams())) {
            $validateData = $this->_validateUpdateForm->getValues();

            $oldName = $this->_categoryService->findNameById($validateData['id']);

            $isUpdateSuccess = $this->_categoryService->updateCategory($validateData['id'], $validateData);
            if ($isUpdateSuccess) {
                $actionMessage = "「{$oldName}」から「{$validateData['name']}」にカテゴリー名を編集しました。";

                $this->_helper->flashMessenger($actionMessage);
            } else {
                //DBの実行に失敗した場合はエラーメッセージがないので、例外エラーメッセージを設定する
                $this->_setExceptionErrorMessages('update');
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!(isset($isUpdateSuccess) && $isUpdateSuccess === true)) {
            $this->_setParam('errorForm', $this->_validateUpdateForm);
            return $this->_forward('index');
        }


        $this->_redirect('/admin/category/index');
    }

    /**
     * カテゴリーを削除するアクションです
     *
     * @return void
     * @author charlesvineyard suzuki-mar
     */
    public function deleteAction()
    {
        //フォームからidが送信されなかったら、indexに遷移する
        if (!$this->_hasParam('id')) {
            $this->_redirect('/admin/category/index');
        }

        //数値以外はエラー
        $validator = new Zend_Validate_Digits($this->_getParam('id'));
        if ($validator->isValid($this->_getParam('id'))) {

            $categoryName = $this->_categoryService->findNameById($this->_getParam('id'));

            $isDeleteSuccess = $this->_categoryService->deleteCategory($this->_getParam('id'));
            //カテゴリーを削除する
            if ($isDeleteSuccess) {
                $this->_helper->flashMessenger("「{$categoryName}」の削除に成功しました");
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!(isset($isDeleteSuccess) && $isDeleteSuccess === true)) {
            $this->_setExceptionErrorMessages('delete');
            $this->_setParam('errorForm', $this->_validateDeleteForm);
            return $this->_forward('index');
        }

        $this->_redirect('/admin/category/index');
    }

    /**
     * 新規作成用のバリデートルールを作成する
     *
     *
     * @return Setuco_Form 新規作成用のフォーム
     * @author suzuki-mar
     */
    private function _createNewValidateForm()
    {

        $form = new Setuco_Form();

        $this->_addNameFormElement($form, 'create');

        return $form;
    }

    /**
     * 編集用のバリデートオブジェクトを作成する
     *
     *
     * @return Setuco_Form 編集用のフォーム
     * @author suzuki-mar
     */
    private function _createUpdateValidateForm()
    {
        //フォームクラスの生成
        $form = new Setuco_Form();

        $this->_addNameFormElement($form, 'update');
        $this->_addIdFormElement($form);
        $this->_addParentIdElement($form);

        return $form;
    }

    /**
     * カテゴリー名のフォームエレメントクラスのインスタンスをフォームクラスに設定する
     *
     * @param Setuco_Form　$form フォームエレメントを追加するフォームクラス
     * @param string $validateType バリデートルールのタイプ create updateのみ指定できる
     * @return void
     * @author suzuki-mar
     * @todo 多段になったらバリデートルールを修正する必要がある
     */
    private function _addNameFormElement(Setuco_Form &$form, $validateType)
    {
        //バリデートタイプで、nameを変更する
        if ($validateType === 'create') {
            $elementName = 'cat_name';
        } else {
            $elementName = 'name';
        }

        $element = $form->createElement('text', $elementName);
        $this->_addCommonFormElementOptions($element);

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('カテゴリー名を入力してください。');
        $validators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 100
                        )
        );
        $stringLength->setMessage('カテゴリー名は%max%文字以下で入力してください。');
        $validators[] = array($stringLength, true);

        $noRecordExistsOption = array('table' => 'category', 'field' => 'name');

        //同じカテゴリーIDの場合のみ、同じカテゴリー名にできる 親IDを変更するときなど
        if ($validateType === 'update') {
            $noRecordExistsOption['exclude'] = array('field' => 'id', 'value' => $this->_getParam('id'));
        }
        $noRecordExists = new Zend_Validate_Db_NoRecordExists($noRecordExistsOption);
        $noRecordExists->setMessage('「%value%」は既に登録されています。');
        $validators[] = array($noRecordExists, true);

        $element->addValidators($validators);

        $form->addElement($element);
    }

    /**
     * カテゴリーIDのフォームエレメントクラスのインスタンスをフォームクラスに追加する
     *
     * @param Setuco_Form　$form フォームエレメントを追加するフォームクラス
     * @return Zend_Form_Element カテゴリーIDのフォームエレメントクラス
     * @author suzuki-mar
     */
    private function _addIdFormElement(Setuco_Form &$form)
    {
        //idをセットするhiddenタグを生成
        $element = $form->createElement('hidden', 'id');
        $this->_addCommonFormElementOptions($element);

        $element->addValidators(array(
            array('NotEmpty', true),
            array('Int')
        ));

        $form->addElement($element);

        return $element;
    }

    /**
     * カテゴリーの親IDのフォームエレメントクラスのインスタンスをフォームクラスに追加する
     *
     * @param Setuco_Form　$form フォームエレメントを追加するフォームクラス
     * @return Zend_Form_Element カテゴリーの親IDのフォームエレメントクラス
     * @author suzuki-mar
     */
    private function _addParentIdElement(Setuco_Form &$form)
    {
        $element = $form->createElement('hidden', 'parent_id');
        $this->_addCommonFormElementOptions($element);

        $element->addValidators(array(
            array('NotEmpty', true),
            array('Int')
        ));

        $form->addElement($element);

        return $element;
    }

    /**
     * フォームエレメントの共通設定をする
     * requiredなどの設定をする
     *
     * @param Zend_Form_Element $element　共通の設定をするフォームエレメントクラス
     * @return void
     */
    private function _addCommonFormElementOptions(&$element)
    {
        $element->setRequired()
                ->addFilter('StringTrim');
    }

    /**
     * フォームにバリデートエラーメッセージをセットする
     * Formクラスのチェックでバリデートエラーとなる場合は、すでにメッセージが設定されているので
     * なにもしない
     *
     * @param  string $validateType createだと新規作成 updateだと編集　deleteだと削除
     * @return void
     * @author suzuki-mar
     */
    private function _setExceptionErrorMessages($validateType)
    {
        if ($validateType === 'create') {
            $validateForm = $this->_validateCreateForm;
        } elseif ($validateType === 'update') {
            $validateForm = $this->_validateUpdateForm;
        } else {
            $validateForm = $this->_validateDeleteForm;
        }

        //例外的なエラー　SQL(DBに登録する)に失敗した場合など　本来は実行されない
        if (!$validateForm->isErrors()) {
            if ($validateType === 'create') {
                $errorMessages['accidental'] = 'カテゴリーの新規作成に失敗しました';
            } elseif ($validateType === 'update') {
                $errorMessages['accidental'] = 'カテゴリーの編集に失敗しました';
            } else {
                $errorMessages['accidental'] = 'カテゴリーの削除に失敗しました';
            }

            $validateForm->setErrorMessages($errorMessages);
            $validateForm->markAsError();
        }
    }

}

