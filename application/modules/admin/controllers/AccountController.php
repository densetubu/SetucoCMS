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
     * サブアカウント一覧表示のアクションです
     *
     * @return void
     * @author suzuki-mar kkyouhei
     */
    public function subindexAction()
    {
        $selectColumns = array('id', 'login_id', 'nickname');
        $this->view->accounts = $this->_accountService->findSortAllAcounts($selectColumns,
                                                                           $this->_getParam('field', 'login_id'),
                                                                           $this->_getParam('order', 'asc'),
                                                                           $this->_getPageNumber(),
                                                                           $this->_getPageLimit()
                                                                          );
        $this->setPagerForView($this->_accountService->countAllAccounts());
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
