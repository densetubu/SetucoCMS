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
 * @author  ece_m
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
     * クラス変数の設定をする
     * @author suzuki-mar
     */
    public function init()
    {
        parent::init();
        $this->_siteService = new Admin_Model_Site();
    }

    /**
     * サイト情報を表示するアクションです
     *
     * @return void
     * @author suzuki-mar
     */
    public function indexAction()
    {
        $siteService = new Admin_Model_Site();
        $this->view->sites = $siteService->getSiteInfo();

        //フラッシュメッセージがある場合のみ設定する
        if ($this->_helper->flashMessenger->hasMessages()) {
            $flashMessages = $this->_helper->flashMessenger->getMessages();
            $this->view->flashMessage = $flashMessages[0];
        }
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
            $this->_redirect('/admin/category/index');
        }

        //バリデートするFormオブジェクトを取得する
        $validateForm = $this->_updateForm();

        //入力したデータをバリデートチェックをする
        if ($validateForm->isValid($this->_getAllParams())) {
           $inputData = $this->_getInputParams();
           unset($inputData['sub']);

            //カテゴリーを編集する
            if ($this->_siteService->updateSite($inputData, $this->_getParam('id'))) {
                $this->_helper->flashMessenger('カテゴリーの編集に成功しました');
                $isSetFlashMessage = true;
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!isset($isSetFlashMessage)) {
            $this->_helper->flashMessenger('カテゴリーの編集に失敗しました');
        }

        $this->_redirect('/admin/site/index');

        return true;
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
                    array('stringLength', true, array(1, 100)),
                ));
        $form->addElement($textElement);

        $urlElement = $form->createElement('text', 'url');
        $urlElement->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
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
                    array('stringLength', true, array(2, 300)),
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
        $form->addElement($commentElement);

        return $form;
    }

}

