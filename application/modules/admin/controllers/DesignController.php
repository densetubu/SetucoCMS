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
     * @var Admin_Model_Site
     */
    private $_siteService;

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
        $this->_siteService = new Admin_Model_Site();
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
        $siteInfos = $this->_siteService->getSiteInfo();

        //空文字以外の入力したものは、入力したものをデフォルト値にする
        if ($this->_hasParam('inputValues')) {
            foreach ($this->_getParam('inputValues') as $key => $value) {
                if ($this->_isInputFiled($key, $value)) {
                    $siteInfos[$key] = $value;
                }
            }
        }

        //blur属性に戻したときに\がエスケープされるので、2重に挿入する
        $siteInfos['name_blur'] = str_replace('\\', '\\\\', $siteInfos['name']);
        $siteInfos['url_blur'] = str_replace('\\', '\\\\', $siteInfos['url']);
        $this->view->sites = $siteInfos;


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
     */
    private function _isInputFiled($filedName, $filedValue)
    {

        if (empty($filedValue)) {
            return false;
        }

        if ($filedName === 'url' && $filedValue === 'http://') {
            return false;
        }

        return true;
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
            $this->_siteService->updateSite($validData, $this->_getParam('id'));
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

        $nameElement = new Zend_Form_Element_Text('name', array(
                    'id' => 'name',
                    'required' => true,
                    'validators' => $this->_makeSiteNameValiDators(),
                    'filters' => array('StringTrim')
                ));
        $form->addElement($nameElement);

        $urlElement = new Zend_Form_Element_Text('url', array(
                    'id' => 'url',
                    'required' => true,
                    'validators' => $this->_makeSiteUrlValiDators(),
                    'filters' => array('StringTrim', 'fullUrl', 'removeSpace')
                ));
        $form->addElement($urlElement);

        $commentElement = new Zend_Form_Element_Text('comment', array(
                    'id' => 'comment',
                    'required' => false,
                    'validators' => $this->_makeCommentValiDators(),
                    'filters' => array('StringTrim')
                ));
        $form->addElement($commentElement);

        $keywordElement = new Zend_Form_Element_Text('keyword', array(
                    'id' => 'keyword',
                    'required' => false,
                    'validators' => $this->_makeKeywordValiDators(),
                    'filters' => array('StringTrim', 'deselectSameKeyword', 'trimKeywords')
                ));

        $form->addElement($keywordElement);

        return $form;
    }

    /**
     * サイト名のバリデートルールを生成する
     *
     * @return array バリデートルールの配列 Zend_Validate_xxx　が要素に入っている
     * @author suzuki-mar
     */
    private function _makeSiteNameValiDators()
    {

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('サイト名を入力してください。');
        $nameValidators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 100
                        )
        );
        $stringLength->setEncoding("UTF-8");
        $stringLength->setMessage('サイト名は%max%文字以下で入力してください。');
        $nameValidators[] = array($stringLength, true);

        return $nameValidators;
    }

    /**
     * サイトのURLのバリデートルールを生成する
     *
     * @return array バリデートルールの配列 Zend_Validate_xxx　が要素に入っている
     * @author suzuki-mar
     */
    private function _makeSiteUrlValiDators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('サイトURLを入力してください。');
        $urlValidators[] = array($notEmpty, true);

        $urlCheck = new Setuco_Validate_Url();
        $urlCheck->setMessage('サイトURLの形式が正しくありません。');
        $urlValidators[] = array($urlCheck, true);

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 50
                        )
        );
        $stringLength->setEncoding("UTF-8");
        $stringLength->setMessage('サイトURLは%max%文字以下で入力してください。');
        $urlValidators[] = array($stringLength, true);

        return $urlValidators;
    }

    /**
     * コメントのバリデートルールを生成する
     *
     * @return array バリデートルールの配列 Zend_Validate_xxx　が要素に入っている
     * @author suzuki-mar
     */
    private function _makeCommentValiDators()
    {

        $stringLength = new Zend_Validate_StringLength(
                        array(
                            'max' => 300
                        )
        );
        $stringLength->setEncoding("UTF-8");
        $stringLength->setMessage('サイトの説明は%max%文字以下で入力してください。');
        $commentValidators[] = array($stringLength, true);

        return $commentValidators;
    }

    /**
     * キーワード用のバリデートルールを生成する
     *
     * @return array バリデートルールの配列 Zend_Validate_xxx　が要素に入っている
     * @author suzuki-mar
     */
    private function _makeKeywordValiDators()
    {
        $stringLength = new Setuco_Validate_KeywordLength(
                        array(
                            'max' => 50,
                            'count_max' => 15
                        )
        );

        $keywordValidators[] = array($stringLength, true);

        return $keywordValidators;
    }

}

