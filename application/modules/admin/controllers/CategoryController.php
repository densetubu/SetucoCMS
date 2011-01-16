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
    private $_newFormValidator = null;
    /**
     * 編集用のバリデーションチェックフォーム
     *
     * @var Setuco_Form
     */
    private $_updateFormValidator = null;
    /**
     * 削除用のフォーム (エラーメッセージ）を入れるだけ
     *
     * @var Setuco_Form
     */
    private $_deleteFormValidator = null;

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
        $this->_newFormValidator = $this->_createNewFormValidator();
        //編集用のバリデートフォーム
        $this->_updateFormValidator = $this->_createUpdateFormValidator();

        //削除用のフォーム（エラーメッセージを入れるだけ)
        $this->_deleteFormValidator = new Setuco_Form();
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
        $this->view->categories = $this->_categoryService->findCategories(
            $this->_getParam('sort'), $this->_getPageNumber(), $this->_getPageLimit()
        );
        $max = $this->_categoryService->countAllCategories();
        $this->setPagerForView($max);

        //バリデートに失敗したエラーフォームがあればセットする
        if ($this->_hasParam('errorForm')) {
            $this->view->errorForm = $this->_getParam('errorForm');
        }

        $this->view->inputCreateCategoryName = $this->_getParam('inputCreateCategoryName', '新規カテゴリー');
    }

    /**
     * カテゴリーを新規作成するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @throws POSTメソッドでアクセスしなかった場合　insert文の実行に失敗した場合
     * @author charlesvineyard suzuki-mar
     */
    public function createAction()
    {
        //フォームから値を送信されなかったら、indexに遷移する
        if (!$this->_request->isPost()) {
            throw new Setuco_Controller_IllegalAccessException('POSTメソッドではありません。');
        }

        //入力したデータがエラーだったら、入力画面に遷移する
        if (!$this->_newFormValidator->isValid($this->_getAllParams())) {
            $this->_setParam('errorForm', $this->_newFormValidator);
            $this->_setParam('inputCreateCategoryName', $this->_getParam('cat_name'));
            return $this->_forward('index');
        }


        $inputData = $this->_newFormValidator->getValues();
        $registData['name'] = $inputData['cat_name'];

        try {
            $this->_categoryService->registCategory($registData);
        } catch (Zend_Exception $e) {
            throw new Setuco_Exception('insert文の実行に失敗しました。' . $e->getMessage());
        }

        $this->_helper->flashMessenger("「{$registData['name']}」を作成しました。");
        $this->_helper->redirector('index');
    }

    /**
     * カテゴリーを更新処理するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @throws POSTメソッドでアクセスしなかった場合、update文の実行に失敗した場合
     * @author charlesvineyard suzuki_mar
     */
    public function updateAction()
    {
        //フォームから値を送信されなかったら、indexに遷移する 直接アクセスの禁止
        if (!$this->_request->isPost()) {
            throw new Setuco_Controller_IllegalAccessException('POSTメソッドではありません。');
        }

        //入力したデータがエラーだたら入力画面に遷移する
        if (!$this->_updateFormValidator->isValid($this->_getAllParams())) {
            $this->_setParam('errorForm', $this->_updateFormValidator);
            return $this->_forward('index');
        }

        $validateData = $this->_updateFormValidator->getValues();

        $oldName = $this->_categoryService->findNameById($validateData['id']);

        try {
            $this->_categoryService->updateCategory($validateData['id'], $validateData);
        } catch (Zend_Exception $e) {
            throw new Setuco_Exception('update文の実行に失敗しました。' . $e->getMessage());
        }

        $actionMessage = "「{$oldName}」を「{$validateData['name']}」に変更しました。";
        $this->_helper->flashMessenger($actionMessage);
        $this->_helper->redirector('index');
    }

    /**
     * カテゴリーを削除するアクションです
     *
     * @return void
     * @throws idパラメーターがない場合　delete文の実行に失敗した場合
     * @author charlesvineyard suzuki-mar
     */
    public function deleteAction()
    {
        if (!$this->_hasParam('id')) {
            throw new Setuco_Controller_IllegalAccessException('パラメータ[id]がありません。');
        }

        //数値以外はエラー
        $validator = new Zend_Validate_Digits($this->_getParam('id'));
        if (!$validator->isValid($this->_getParam('id'))) {

            $this->_setExceptionErrorMessages('delete');
            $this->_setParam('errorForm', $this->_deleteFormValidator);
            return $this->_forward('index');
        }

        $categoryName = $this->_categoryService->findNameById($this->_getParam('id'));

        try {
            $this->_categoryService->deleteCategory($this->_getParam('id'));
        } catch (Zend_Exception $e) {
            throw new Setuco_Exception('delete文の実行に失敗しました。' . $e->getMessage());
        }

        $this->_helper->flashMessenger("「{$categoryName}」を削除しました。");

        $this->_helper->redirector('index');
    }

    /**
     * 新規作成用のバリデートルールを作成する
     *
     *
     * @return Setuco_Form 新規作成用のフォーム
     * @author suzuki-mar
     */
    private function _createNewFormValidator()
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
    private function _createUpdateFormValidator()
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
        $this->_addFormElementCommonOptions($element);

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
        $this->_addFormElementCommonOptions($element);

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
        $this->_addFormElementCommonOptions($element);

        $element->addValidators(array(
            array('NotEmpty', true),
            array('Int')
        ));

        $form->addElement($element);

        return $element;
    }

}

