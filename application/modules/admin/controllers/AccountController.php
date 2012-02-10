<?php

/**
 * 管理側のアカウント情報編集のコントローラーです。
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
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki-mar
 */

/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      suzuki-mar
 */
class Admin_AccountController extends Setuco_Controller_Action_AdminAbstract
{

    /**
     * アカウント情報のサービスクラス
     *
     * @var Admin_Model_Account
     */
    private $_accountService;

    /**
    * 新規ユーザ情報をチェックするバリデートフォームクラス
    *
    * @var Setuco_Form
    */
    private $_newAccountFormValidator;

    /**
     * パスワード情報をチェックするバリデートフォームクラス
     *
     * @var Setuco_Form
     */
    private $_updatePasswordFormValidator;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->_accountService = new Admin_Model_Account();

        $this->_newAccountFormValidator = $this->_createNewAccountFormValidator();

        $this->_updatePasswordFormValidator = $this->_createUpdatePasswordFormValidator();
    }

    /**
     * アカウント情報を表示するアクションです
     *
     * @return void
     * @author suzuki-mar
     */
    public function indexAction()
    {
        // フラッシュメッセージ設定
        $this->_showFlashMessages();

        //バリデートに失敗したエラーフォームがあればセットする
        if ($this->_hasParam('errorForm')) {
            $this->view->errorForm = $this->_getParam('errorForm');
        }
    }

    /**
     * アカウント新規追加を表示するアクションです
     *
     * @return void
     * @author ErinaMikami
     */
    public function formAction()
    {
        // フラッシュメッセージ設定
        $this->_showFlashMessages();

        //バリデートに失敗したエラーフォームがあればセットする
        if ($this->_hasParam('errorForm')) {
            $this->view->errorForm = $this->_getParam('errorForm');
        }
    }

    /**
    *ユーザを新規追加するアクションです
    *
    * @return void
    * @throws POSTメソッドでアクセスしなかった場合 insert文の実行に失敗した場合
    * @author kkyouhei
    */
    public function createAction()
    {

        //フォームから値を送信されなかったら、エラーページに遷移する
        if (!$this->_request->isPost()) {

            throw new Setuco_Controller_IllegalAccessException('POSTメソッドではありません。');
        }
        
        if(!$this->_newAccountFormValidator->isValid($this->_getAllParams())) {
            $this->_setParam('errorForm', $this->_newAccountFormValidator);
            $this->view->assign('inputAccountId', $this->_getParam('sub_account'));
            $this->view->assign('inputAccountNickName', $this->_getParam('sub_nickname'));
            return $this->_forward('form');
        }

        $inputData = $this->_newAccountFormValidator->getValues();
        $registData['login_id'] = $inputData['sub_account'];
        $registData['nickname'] = $inputData['sub_nickname'];
        $registData['password'] = $inputData['user_pass'];
        try {
            $this->_accountService->registAccount($registData);
        } catch (Zend_Exception $e) {
            throw new Setuco_Exception('insert文の実行に失敗しました。' . $e->getMessage());
        }
        $this->_helper->flashMessenger("「{$registData['nickname']}」を作成しました");
        $this->_helper->redirector('form');
    }

    /**
     * パスワード情報を変更するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author suzuki-mar
     */
    public function updateAction()
    {
        //フォームから値を送信されなかったら、エラーページに遷移する
        if (!$this->_request->isPost()) {
            throw new Setuco_Controller_IllegalAccessException('POSTメソッドではありません。');
        }

        //入力したデータをバリデートチェックをする
        if (!$this->_isPasswordValid($this->_getAllParams())) {
            $this->_setParam('errorForm', $this->_updatePasswordFormValidator);
            return $this->_forward('index');
        }

        $validData = $this->_updatePasswordFormValidator->getValues();

        try {
            $this->_accountService->updatePassword($validData['user_pass'], $this->_getAccountInfos('login_id'));
        } catch (Zend_Exception $e) {
            throw new Setuco_Exception('update文の実行に失敗しました。' . $e->getMessage());
        }

        $this->_helper->flashMessenger('パスワードを編集しました。');
        $this->_helper->redirector('index');
    }

    /**
    * 新規ユーザを追加するバリデーションチェックするフォームクラスのインスタンスを生成する
    *
    *
    * @return Zend_Form
    * @author kkyouhei
    */
    private function _createNewAccountFormValidator()
    {
        $form = new Setuco_Form();

        $subAccount = new Zend_Form_Element_Text('sub_account', array(
                     'id'        => 'sub_account',
                     'required'  => true,
                     'validators'=> $this->_makeAccountIdValidators(),
                     'filters'   => array('StringTrim')
        ));

        $subNickName = new Zend_Form_Element_Text('sub_nickname', array(
                     'id'        => 'sub_nickname',
                     'required'  => true,
                     'validators'=> $this->_makeAccountNickNameValidators(),
                     'filters'   => array('StringTrim')
        ));

        $userPass = new Zend_Form_Element_Password('user_pass', array(
                     'id'  => 'user_pass',
                     'required'  => true,
                     'validators'=> $this->_makeAccountPasswordValidators(),
                     'filters'   => array('StringTrim')
        ));

        $form->addElements(array($subAccount, $subNickName, $userPass));

        return $form;
    }

    /**
    * ログインIDのバリデートルールを作成する
    *
    *
    * @return Zend_Validate_NotEmpty Setuco_Validate_StringLength Zend_Valid_Db_NoRecordExists
    * @author kkyouhei
    */

    private function _makeAccountIdValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('ログインIDを入力してください。');
        $validators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'min' => 4,
                            'max' => 30
                        )
        );
        $stringLength->setEncoding('UTF-8');
        $stringLength->setMessage('ログインIDは4文字以上30文字以下で入力してください。');
        $validators[] = array($stringLength, true);

        $noRecordExistsOption = array('table' => 'account', 'field' => 'login_id');
        $noRecordExists = new Zend_Validate_Db_NoRecordExists($noRecordExistsOption);
        $noRecordExists->setMessage( '「%value%」は既に登録されています。' );
        $validators[] = array($noRecordExists, true);

        return $validators;
    }

    /**
    * ニックネームのバリデートルールを作成する
    *
    *
    * @return Zend_Validate_NotEmpty Setuco_Validate_StringLength Zend_Valid_Db_NoRecordExists
    * @author kkyouhei
    */

    private function _makeAccountNickNameValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('ニックネームを入力してください。');
        $validators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max'=>16
                        )
        );
        $stringLength->setMessage('ニックネームは16文字以下で入力してください。');
        $validators[] = array($stringLength, true);

        $noRecordExistsOption = array('table' => 'account', 'field' => 'nickname');
        $noRecordExists = new Zend_Validate_Db_NoRecordExists($noRecordExistsOption);
        $noRecordExists->setMessage('「%value%」は既に登録されています。');
        $validators[] = array($noRecordExists, true);

        return $validators;
    }

    /**
    * パスワードのバリデートルールを取得する
    *
    *
    * @return Zend_NotEmpty Zend_Validate_StringLength Setuco_Validate_Match
    * @author kkyouhei
    */

    private function _makeAccountPasswordValidators()
    {

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('パスワードを入力してください。');
        $validators[] = array($notEmpty);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'min' => 6,
                            'max' => 30
                        )
        );
        $stringLength->setMessage('パスワードは%min%文字以上%max%文字以下で入力してください。');
        $validators[] = array($stringLength);

        $confirmCheck = new Setuco_Validate_Match(
                        array(
                            'check_key' => 'pass_check'
                        )
        );
        $confirmCheck->setMessage('パスワードとパスワード確認が一致しません。');
        $validators[] = $confirmCheck;

        return $validators;

    }

    /**
     * パスワードを変更するバリデートチェックするフォームクラスのインスタンスを生成します
     *
     *
     * @return Zend_Form
     * @author suzuki-mar
     */
    private function _createUpdatePasswordFormValidator()
    {
        $form = new Setuco_Form();


        $passElement = $form->createElement('text', 'user_pass');
        $this->_addFormElementCommonOptions($passElement);

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('新しいパスワードを入力してください。');
        $passValidators[] = array($notEmpty);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'min' => 6,
                            'max' => 30
                        )
        );
        $stringLength->setMessage('パスワードは%min%文字以上%max%文字以下で入力してください。');
        $passValidators[] = array($stringLength);

        $confirmCheck = new Setuco_Validate_Match(
                        array(
                            'check_key' => 'pass_check'
                        )
        );
        $confirmCheck->setMessage('新しいパスワードとパスワード確認が一致しません。');
        $passValidators[] = $confirmCheck;

        $passwordCheck = new Setuco_Validate_Password();
        $passValidators[] = $passwordCheck;

        $passElement->addValidators($passValidators);
        $form->addElement($passElement);

        return $form;
    }

    /**
     * パスワード入力のバリデートチェックをする
     *
     * @param array 入力したデータ
     * @return boolean バリデートできたか
     * @author suzuki-mar
     */
    private function _isPasswordValid($inputParams)
    {
        $results['password'] = $this->_updatePasswordFormValidator->isValid($inputParams);

        if (!$this->_accountService->isSamePassword($this->_getParam('pass_old'), $this->_getAccountInfos('login_id'))) {

            $allErrorMessages[] = '現在のパスワードが一致しません。';

            //エラーメッセージが初期化されてしまうので、一時変数を作成してからまとめてメッセージをセットしなおす。
            if ($results['password'] === false) {
                foreach($this->_updatePasswordFormValidator->getMessages() as $filedName => $errorMessages) {
                    foreach ($errorMessages as $message) {
                        $allErrorMessages[] = $message;
                    }
                }
            }

            $this->_updatePasswordFormValidator->addErrors($allErrorMessages);
            $results['old_password'] = false;
        }

        return !in_array(false, $results);
    }
}
