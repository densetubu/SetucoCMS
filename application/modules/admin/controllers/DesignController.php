<?php

/**
 * 管理モジュールのコントローラーで、閲覧画面のデザインを管理するコントローラーです。
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
class Admin_DesignController extends Setuco_Controller_Action_AdminAbstract
{

    /**
     * Designのサービスクラス
     *
     * @var Admin_Model_Design
     */
    private $_designService;

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
        $this->_designService = new Admin_Model_Design();
        $this->_updateFormValidator = $this->_createUpdateFormValidator();
    }

    /**
     * サイト情報を表示するアクションです
     *
     * @return void
     * @author suzuki-mar
     */
    public function indexAction()
    {
        $this->view->designInfos = $this->_designService->findAllDesignInfos();
        $this->view->currentDesignName = $this->_designService->findSelectedDesignName();

        //バリデートに失敗したエラーフォームがあればセットする
        if ($this->_hasParam('errorForm')) {
            $this->view->errorForm = $this->_getParam('errorForm');
        }

        //フラッシュメッセージを設定する
        $this->_showFlashMessages();
    }

    /**
     * サイト情報の更新処理のアクションです。
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

        //サイト情報を編集する
        try {
            $this->_designService->updateDesign($validData);
        } catch (Zend_Exception $e) {
            throw new Setuco_Exception('update文の実行に失敗しました。' . $e->getMessage());
        }

        $this->_helper->flashMessenger('サイト情報を編集しました。');
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

        $nameElement = new Zend_Form_Element_Text('design_name', array(
                    'id' => 'design_name',
                    'required' => true,
                    'validators' => $this->_makeDesignNameValiDators(),
                    'filters' => array('StringTrim')
                ));
        $form->addElement($nameElement);

        return $form;
    }

    /**
     * デザイン名のバリデートルールを生成する
     *
     * @return array バリデートルールの配列 Zend_Validate_xxx　が要素に入っている
     * @author suzuki-mar
     */
    private function _makeDesignNameValiDators()
    {

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('デザインを選択してください。');
        $nameValidators[] = array($notEmpty, true);


        return $nameValidators;
    }


}

