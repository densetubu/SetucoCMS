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

        $this->_validateUpdateForm = $this->_createValidateUpdateForm();
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
            try {
                $this->_siteService->updateSite($validateData, $this->_getParam('id'));
            } catch(Zend_Exception $e) {
                throw new Setuco_Exception('update文の実行に失敗しました。' . $e->getMessage());
            }

            $isUpdateSuccess = true;
            $this->_helper->flashMessenger('サイト情報を編集しました。');
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
     * バリデートするフォームクラスのインスタンスを生成します
     *
     *
     * @return Zend_Form
     * @author suzuki-mar
     */
    private function _createValidateUpdateForm()
    {
        $form = new Setuco_Form();

        $textElement = $form->createElement('text', 'name');
        $this->_addFormElementCommonOptions($textElement);

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('サイト名を入力してください。');
        $nameValidators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 100
                        )
        );
        $stringLength->setMessage('サイト名は%max%文字以下で入力してください。');
        $nameValidators[] = array($stringLength, true);

        $textElement->addValidators($nameValidators);
        $form->addElement($textElement);


        $urlElement = $form->createElement('text', 'url');

        $this->_addFormElementCommonOptions($urlElement);
        $urlElement->addFilter('fullUrl');

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('サイトURLを入力してください。');
        $urlValidators[] = array($notEmpty, true);

        $urlCheck = new Setuco_Validate_Url();
        $urlCheck->setMessage('サイトURLの形式が正しくありません。');
        $urlValidators[] = array($urlCheck, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'min' => 8,
                            'max' => 100
                        )
        );
        $stringLength->setMessage('サイトURLは、%min%文字以上%max%文字以下で入力してください。');
        $urlValidators[] = array($stringLength, true);
        $urlElement->addValidators($urlValidators);

        $form->addElement($urlElement);


        $commentElement = $form->createElement('text', 'comment');
        $this->_addFormElementCommonOptions($commentElement, array('required' => false));
        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 300
                        )
        );
        $stringLength->setMessage('サイトの説明は%max%文字以下で入力してください。');
        $commentValidators[] = array($stringLength, true);
        $commentElement->addValidators($commentValidators);

        $form->addElement($commentElement);


        $keywordElement = $form->createElement('text', 'keyword');
        $this->_addFormElementCommonOptions($keywordElement, array('required' => false));
        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 300
                        )
        );
        $stringLength->setMessage('キーワードは%max%文字以下で入力してください。');
        $keywordValidators[] = array($stringLength, true);
        $keywordElement->addValidators($keywordValidators);

        $form->addElement($keywordElement);


        return $form;
    }

}

