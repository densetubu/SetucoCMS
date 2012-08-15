<?php

/**
 * 管理側のフリースペース編集のコントローラーです。
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
 * @since       File available since Release 1.5.0
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
class Admin_FreeSpaceController extends Setuco_Controller_Action_AdminAbstract
{

    /**
     * Siteのサービスクラス
     *
     * @var Admin_Model_FreeSpace
     */
    private $_freeSpaceService;

    /**
     * 編集のバリデートフォーム
     *
     * @var Setuco_Form
     */
    private $_updateFormValidator;


    /**
     * クラス変数の設定をする
     *
     * @author suzuki-mar
     */
    public function init()
    {
        parent::init();
        $this->_freeSpaceService = new Admin_Model_FreeSpace();
        $this->_updateFormValidator = $this->_createUpdateFormValidator();
    }

    /**
     * フリースペースを表示するアクションです
     *
     * @return void
     * @author suzuki-mar
     * @todo 入力エラーの場合にDBに保存してあるデータを取得するメソッドを親に作成する
     */
    public function indexAction()
    {
        $freeSpaces = $this->_freeSpaceService->getFreeSpaceInfo();

        //空文字以外の入力したものは、入力したものをデフォルト値にする
        if ($this->_hasParam('inputValues')) {
            foreach ($this->_getParam('inputValues') as $key => $value) {
                if ($this->_isInputFiled($key, $value)) {
                    $freeSpaces[$key] = $value;
                }
            }
        }
        
        $this->view->freeSpaces = $freeSpaces;


        //バリデートに失敗したエラーフォームがあればセットする
        if ($this->_hasParam('errorForm')) {
            $this->view->errorForm = $this->_getParam('errorForm');
        }

        //フラッシュメッセージを設定する
        $this->_showFlashMessages();
    }

    /**
     * 入力した項目かを調べる
     *
     * @param string $filedName 入力した項目の名前
     * @param string $filedValue　入力した項目の値
     * @return boolean 入力した項目か
     * @author suzuki-mar
     * @todo 各コントローラー毎に定義してしまっているので共通化する
     */
    private function _isInputFiled($filedValue)
    {

        if (empty($filedValue)) {
            return false;
        }

        return true;
    }

    /**
     * フリースペースの更新処理のアクションです。
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
        if (!$this->_updateFormValidator->isValid($this->_getAllParams())) {
            $this->_setParam('inputValues', $this->_updateFormValidator->getValues());
            $this->_setParam('errorForm', $this->_updateFormValidator);
            return $this->_forward('index');
        }

        $validData = $this->_updateFormValidator->getValues();
        
        //フリースペース情報を編集する
        try {
            $this->_freeSpaceService->updateFreeSpace($validData);
        } catch (Zend_Exception $e) {
            throw new Setuco_Exception('update文の実行に失敗しました。' . $e->getMessage());
        }

        $this->_helper->flashMessenger('フリースペースを編集しました。');
        $this->_helper->redirector('index');
    }

    /**
     * バリデートするフォームクラスのインスタンスを生成します
     *
     *
     * @return Zend_Form
     * @author suzuki-mar
     */
    private function _createUpdateFormValidator()
    {
        $form = new Setuco_Form();

        $titleElement = new Zend_Form_Element_Text('title', array(
                    'id' => 'title',
                    'required' => true,
                    'validators' => $this->_makeTitleValiDators(),
                    'filters' => array('StringTrim')
                ));
        $form->addElement($titleElement);

        $contentElement = new Zend_Form_Element_Text('content', array(
                    'id' => 'content',
                    'required' => true,
                    'validators' => $this->_makeContentValiDators(),
                    'filters' => array('StringTrim')
                ));
        $form->addElement($contentElement);

        return $form;
    }

    /**
     * タイトルのバリデートルールを生成する
     *
     * @return array バリデートルールの配列 Zend_Validate_xxx　が要素に入っている
     * @author ErinaMikami
     */
    private function _makeTitleValiDators()
    {

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('タイトルを入力してください。');
        $titleValidators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 100
                        )
        );
        $stringLength->setEncoding("UTF-8");
        $stringLength->setMessage('タイトルは%max%文字以下で入力してください。');
        $titleValidators[] = array($stringLength, true);

        return $titleValidators;
    }

    /**
     * コンテンツのバリデートルールを生成する
     *
     * @return array バリデートルールの配列 Zend_Validate_xxx　が要素に入っている
     * @author suzuki-mar
     */
    private function _makeContentValiDators()
    {

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('コンテンツを入力してください。');
        $contentValidators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => Setuco_Data_Constant_FreeSpace::MAX_STRING_LENGTH
                        )
        );
        $stringLength->setEncoding("UTF-8");
        $stringLength->setMessage('コンテンツは%max%文字以下で入力してください。');
        $contentValidators[] = array($stringLength, true);

        return $contentValidators;
    }

}

