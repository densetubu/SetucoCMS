<?php

/**
 * 管理側のサイト情報編集のコントローラーです。
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
class Admin_SiteController extends Setuco_Controller_Action_AdminAbstract
{

    /**
     * Siteのサービスクラス
     * @var Admin_Model_Site
     */
    private $_siteService;

    /**
     * 編集のバリデートフォーム
     *
     * @var Setuco_Form
     */
    private $_validateUpdateForm;

    /**
     * クラス変数の設定をする
     * @author suzuki-mar
     */
    public function init()
    {
        parent::init();
        $this->_siteService = new Admin_Model_Site();

        $this->_validateUpdateForm = $this->_updateForm();
    }

    /**
     * サイト情報を表示するアクションです
     *
     * @return void
     * @author suzuki-mar
     */
    public function indexAction()
    {
        $this->view->sites = $this->_getParam('inputValues', $this->_siteService->getSiteInfo());

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
        //フォームから値を送信されなかったら、indexに遷移する
        if (!$this->_request->isPost()) {
            $this->_helper->redirector('index');
        }

        //入力したデータをバリデートチェックをする
        if ($this->_validateUpdateForm->isValid($this->_getAllParams())) {

            $validateData = $this->_validateUpdateForm->getValues();

            //サイト情報を編集する
            $isUpdateSuccess = $this->_siteService->updateSite($validateData, $this->_getParam('id'));
            if ($isUpdateSuccess) {
                $this->_helper->flashMessenger('サイト情報を編集しました。');
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if ( !(isset($isUpdateSuccess) && $isUpdateSuccess === true)) {
            $this->_setParam('inputValues', $_POST);
            $this->_setParam('errorForm', $this->_validateUpdateForm);
            return $this->_forward('index');
        }

        $this->_helper->redirector('index');
    }

    /**
     * フォームの雛形を作成します。
     *
     * @return Zend_Form
     */
    private function _updateForm()
    {
        $form = new Setuco_Form();
        $form->setMethod('post');

        $textElement = $form->createElement('text', 'name');
        $textElement->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
                    //文字列の長さを指定する
                    array('stringLength', true, array(20, 100)),
                ));
        $form->addElement($textElement);

        $urlElement = $form->createElement('text', 'url');

        $urlElement->addPrefixPath('Setuco_Validator', 'Setuco/Validator', 'validate');
        $urlElement->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
                    array('url', true),
                    //文字列の長さを指定する
                    array('stringLength', true, array(6, 30)),
                ));
        $form->addElement($urlElement);

        $commentElement = $form->createElement('text', 'comment');
        $commentElement->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
                    //文字列の長さを指定する
                    array('stringLength', true, array(20, 300)),
                ));
        $form->addElement($commentElement);


        $keywordElement = $form->createElement('text', 'keyword');
        $keywordElement->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
                    //文字列の長さを指定する
                    array('stringLength', true, array(2, 300)),
                ));
        $form->addElement($keywordElement);
        return $form;
    }

}

