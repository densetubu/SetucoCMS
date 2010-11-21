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

        //バリデートするFormオブジェクトを取得する
        $validateForm = $this->_updateForm();

        //入力したデータをバリデートチェックをする
        if ($validateForm->isValid($this->_getAllParams())) {

           $validateData = $validateForm->getValues();
           
            //カテゴリーを編集する
            if ($this->_siteService->updateSite($validateData, $this->_getParam('id'))) {
                $this->_helper->flashMessenger('サイト情報を編集しました。');
                $isSetFlashMessage = true;
            }
        }

        //フラッシュメッセージを保存していない場合は、エラーメッセージを保存する
        if (!isset($isSetFlashMessage)) {

            //フォームのnameと項目の名前の対応表
            $fields = array('name' => 'サイト名', 'url' => 'サイトURL', 'comment' => '説明', 'keyword' => 'キーワード');

            foreach ($validateForm->getMessages() as $field => $messages) {

                foreach ($messages as $value) {
                    $message = "{$fields[$field]} : {$value}";
                    $this->_helper->flashMessenger->addMessage($message);
                }
            }
            unset($value);
        }

        $this->_helper->redirector('index');
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
                    array('stringLength', true, array(20, 100)),
                ));
        $form->addElement($textElement);

        $urlElement = $form->createElement('text', 'url');

        $urlElement->addPrefixPath('Setuco_Validator', 'Setuco/Validator', 'validate');
        $urlElement->setRequired()
                ->addFilter('StringTrim')
                ->addValidators(array(
                    array('NotEmpty', true),
                    array('url',true),
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

