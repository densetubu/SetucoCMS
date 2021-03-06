<?php

/**
 * 管理側のカテゴリーページのコントローラー
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category    Setuco
 * @package     Admin
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

        $name = new Zend_Form_Element_Text('cat_name', array(
            'id'         => 'cat_name',
            'required'   => true,
            'validators' => $this->_makeNameValidators('create'),
            'filters'    => array('StringTrim')
        ));
        
        $form->addElement($name);

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

        $name = new Zend_Form_Element_Text('name', array(
            'id'         => 'name',
            'required'   => true,
            'validators' => $this->_makeNameValidators('update'),
            'filters'    => array('StringTrim')
        ));

         $id = new Zend_Form_Element_Hidden('id', array(
            'id'    => 'id',
            'required'   => true,
            'validators' => $this->_makeIdValidators('update'),
            'filters'    => array('StringTrim')

        ));

        $parentId = new Zend_Form_Element_Hidden('parent_id', array(
            'id'    => 'parent_id',
            'required'   => true,
            'validators' => $this->_makeParentIdValidators('update'),
            'filters'    => array('StringTrim')
        ));

        $form->addElements(array($name, $id, $parentId));
        
        return $form;
    }

    /**
     * カテゴリー名のバリデートルールを取得する
     *
     * @param string $validateType バリデートルールのタイプ create updateのみ指定できる
     * @return void
     * @author suzuki-mar
     * @todo 多段になったらバリデートルールを修正する必要がある
     */
    private function _makeNameValidators($validateType)
    {

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('カテゴリー名を入力してください。');
        $validators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 100
                        )
        );
        $stringLength->setEncoding("UTF-8");
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

        return $validators;
    }

    /**
     * IDのバリデートルールを取得する
     *
     * @return Zend_Form_Element カテゴリーIDのフォームエレメントクラス
     * @author suzuki-mar
     */
    private function _makeIdValidators()
    {
        $validators[] = new Zend_Validate_NotEmpty();
        $validators[] = new Zend_Validate_Int();

        return $validators;
    }

    /**
     * parentIDのバリデートルールを取得する
     *
     * @param Setuco_Form　$form フォームエレメントを追加するフォームクラス
     * @return Zend_Form_Element カテゴリーの親IDのフォームエレメントクラス
     * @author suzuki-mar
     */
    private function _makeParentIdValidators()
    {
        
        $validators[] = new Zend_Validate_NotEmpty();
        $validators[] = new Zend_Validate_Int();

        return $validators;
    }

}

