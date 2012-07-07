<?php
/**
 * インストーラのコントローラ
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
 * @category   Setuco
 * @package    Install
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @since      File available since Release 1.2.0
 * @author     Takayuki Otake 
 */

/**
 * インストールをするコントローラ
 *
 * @package    Install
 * @subpackage Controller
 * @author Takayuki Otake
 */
class Install_InstallController
    extends Setuco_Controller_Action_InstallAbstract
{

    /**
     * アクションの共通設定
     * @author Takayuki Otake
     */
    function init()
    {
        parent::init();
        $this->_session = new Zend_Session_Namespace('Setuco_Install_Service');
        $this->_initializeFormValidator = $this->_createInitializeFormValidator();
    }

    /**
     * 入力フォームを表示するアクション
     *
     * @author Takayuki Otake
     * @return void
     */
    public function indexAction()
    {
        $inputValues = $this->_getAllParams();

        $defaultValues = $this->_getDefaultValues();
        foreach ($defaultValues as $key => $value) {
            if (empty($inputValues[$key])) {
                $inputValues[$key] = $defaultValues[$key];
            }
        }
        unset($defaultValues);

        $this->view->inputValues = $inputValues;
    }

    /**
     * 入力内容の確認をするアクション
     *
     * @author Takayuki Otake
     * @return void
     */
    public function confirmAction()
    {
        $inputValues = $this->_getAllParams();
        $this->_setSession($inputValues);

        if (false === $this->_request->isPost()) {
            return $this->_helper->redirector('index', 'install', null);
        }

        if (false === $this->_initializeFormValidator->isValid($inputValues)) {
            return $this->_helper->redirector('index', 'install', null);
        }

        $this->view->inputValues = $inputValues;
    }

    /**
     * 入力したデータから初期化の実行をするアクション
     *
     * @author Takayuki Otake
     */
    public function actionAction()
    {
        if (false === $this->_request->isPost()) {
            return $this->_helper->redirector('index', 'install', null);
        }

        $inputValues = $this->_getAllParams();
        if (preg_match("/^http(s):\/\//", $inputValues['site_url']) === false) {
            $inputValues['site_url'] = 'http://' . $inputValues['site_url'];
        }

        if (false === $this->_initializeFormValidator->isValid($inputValues)) {
            return $this->_helper->redirector('index', 'install', null);
        }

        $inputValues['site_id'] = 1;
        //TODO: 例外キャッチでindexActionへ遷移
        $dbService = new Install_Model_Db($inputValues);
        $dbService->setupSchema();
        $dbService->updateSite($inputValues);
        $dbService->updateAccount($inputValues);

        Install_Model_Config::updateApplicationConfig($inputValues);

        //二重送信防止
        $this->_helper->redirector('finish', 'install', null);
    }

    /**
     * セットアップ終了ページ
     *
     * @author Takayuki Otake
     * @return void
     */
    public function finishAction()
    {
        $_siteService = new Admin_Model_Site();
        $siteInfos = $_siteService->getSiteInfo();

        $siteInfos['url'] = preg_replace('/\/$/', '', $siteInfos['url']);

        $this->view->siteInfos = $siteInfos;
        Zend_Session::destroy();
    }

    /**
     * 入力フォームのデフォルト値を取得する関数
     *
     * @author Takayuki Otake
     * @return array String
     */
    private function _getDefaultValues()
    {
        if ($_SERVER['SERVER_ADDR'] == '::1') {
            $addr = 'localhost';
        } else {
            $addr = $_SERVER['SERVER_ADDR'];
        }
        return array(
                'account_id' => '',
                'site_url' => 'http://' . $addr . $this->view->baseUrl('/'),
                'site_name' => 'サイト名を設定してください',
                'site_comment' => 'サイトの説明を設定してください。',
                'db_host' => 'localhost',
                'db_name' => '',
                'db_user' => '',
                'db_pass' => ''
                );
    }

    /**
     * セッションに値をセットする関数
     *
     * @author Takayuki Otake
     * @return void
     **/
    private function _setSession($values)
    {
        foreach ($values as $key => $value) {
            if (preg_match("/pass/", $key)) {
                unset($this->_session->$key);
                continue;
            }

            $this->_session->$key = $values[$key];
        }
    }

    /**
     * セッションの値を取得する関数
     *
     * @author Takayuki Otake
     * @return array String
     */
    private function _getSession()
    {
        foreach ($this->_getDefaultValues() as $key => $value) {
            $params[$key] = $this->_session->$key;
        }

        return $params;
    }

    /**
     * インストーラのZend_Form
     *
     * @author Takayuki Otake
     * @return Setuco_Form
     */
    private function _createInitializeFormValidator()
    {
        $form = new Setuco_Form();

        $adminAccountIdElement = new Zend_Form_Element_Text(
            'account_id',
            array(
                    'id' => 'account_id',
                    'required' => 'true',
                    'validators' => $this->_makeAdminAccountIdValidators(),
                    'filters' => array('StringTrim')
                ));
        $form->addElement($adminAccountIdElement);

        $adminAccountPassElement = new Zend_Form_Element_Text(
            'account_pass',
            array(
                    'id' => 'account_pass',
                    'required' => 'true',
                    'validators' => $this->_makeAdminAccountPasswordValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($adminAccountPassElement);

        $adminAccountPassCheckElement = new Zend_Form_Element_Text(
            'account_pass_check', 
            array(
                    'id' => 'account_pass_check',
                    'required' => 'true',
                    'validators' => $this->_makeAdminAccountPasswordValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($adminAccountPassCheckElement);

        $siteNameElement = new Zend_Form_Element_Text(
            'site_name',
            array(
                    'id' => 'site_name',
                    'required' => 'true',
                    'validators' => $this->_makeSiteNameValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($siteNameElement);

        $siteCommentElement = new Zend_Form_Element_Text(
            'site_comment', 
            array(
                    'id' => 'site_comment',
                    'required' => 'true',
                    'validators' => $this->_makeSiteCommentValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($siteCommentElement);

        $siteUrlElement = new Zend_Form_Element_Text(
            'site_url', 
            array(
                    'id' => 'site_url',
                    'required' => 'true',
                    'validators' => $this->_makeSiteUrlValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($siteUrlElement);

        $dbHostElement = new Zend_Form_Element_Text(
            'db_host', 
            array(
                    'id' => 'db_host',
                    'required' => 'true',
                    'validators' => $this->_makeDbHostValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($dbHostElement);

        $dbNameElement = new Zend_Form_Element_Text(
            'db_name', 
            array(
                    'id' => 'db_name',
                    'required' => 'true',
                    'validators' => $this->_makeDbNameValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($dbNameElement);

        $dbUserElement = new Zend_Form_Element_Text(
            'db_user', 
            array(
                    'id' => 'db_user',
                    'required' => 'true',
                    'validators' => $this->_makeDbUserValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($dbUserElement);

        $dbPassElement = new Zend_Form_Element_Text(
            'db_pass', 
            array(
                    'id' => 'db_pass',
                    'required' => 'true',
                    'validators' => $this->_makeDbPassValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($dbPassElement);

        return $form;
    }

    /**
     * サイト名のバリデータ
     * 
     * @author Takayuki Otake
     * @return Zend_Form
     */
    private function _makeSiteNameValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('サイト名を入力してください。');
        $siteNameValidators[] = array($notEmpty, true);

        $stringLength = new Zend_Validate_StringLength(
                array(
                    'max' => 100
                    )
                );
        $stringLength->setEncoding("UTF-8");
        $stringLength->setMessage(
            'サイト名は%max%文字以下で入力してください。'
        );
        $siteNameValidators[] = array($stringLength, true);

        return $siteNameValidators;
    }

    /**
     * サイト説明のバリデータ
     * 
     * @author Takayuki Otake
     * @return Zend_Form
     */
    private function _makeSiteCommentValidators()
    {
        $stringLength = new Zend_Validate_StringLength(
                array(
                    'max' => 300
                    )
                );
        $stringLength->setEncoding("UTF-8");
        $stringLength->setMessage(
            'サイトの説明は%max%文字以下で入力しだください。'
        );
        $commentValidators[] = array($stringLength, true);

        return $commentValidators;
    }

    /**
     * 管理者のログインIDのバリデータ
     * 
     * @author Takayuki Otake
     * @return Zend_Form
     */
    private function _makeAdminAccountIdValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage(
            '管理者アカウントのログインIDを入力してください'
        );
        $idValidators[] = array($notEmpty, true);

        return $idValidators;
    }

    /**
     * 管理者のログインパスワードのバリデータ
     * 
     * @author Takayuki Otake
     * @return Zend_Form
     */
    private function _makeAdminAccountPasswordValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage(
            '管理者アカウントのパスワードを入力してください。'
        );
        $passValidators[] = array($notEmpty);

        $stringLength = new Zend_Validate_StringLength(
                array(
                    'min' => 6,
                    'max' => 30
                    )
                );
        $stringLength->setMessage(
            'パスワードは%min%文字以上%max%文字以下で入力してください。'
        );
        $passValidators[] = array($stringLength);

        $confirmCheck = new Setuco_Validate_Match(
                array(
                    'check_key' => 'account_pass_check'
                    )
                );
        $confirmCheck->setMessage(
            'パスワードとパスワード確認が一致しません。'
        );
        $passValidators[] = $confirmCheck;

        $passwordCheck = new Setuco_Validate_Password();
        $passValidators[] = $passwordCheck;

        return $passValidators;
    }

    /**
     * サイトURLのバリデータ
     *
     * @author Takayuki Otake
     * @return Zend_Form
     */
    private function _makeSiteUrlValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage(
            '接続するデータベースのアドレスを入力してください。'
        );
        $urlValidators[] = array($notEmpty, true);

        return $urlValidators;
    }

    /**
     * データベースホストのバリデータ
     *
     * @author Takayuki Otake
     * @return Zend_Form
     */
    private function _makeDbHostValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage(
            '接続するデータベースのアドレスを入力してください。'
        );
        $hostValidators[] = array($notEmpty, true);

        return $hostValidators;
    }

    /**
     * データベース名のバリデータ
     *
     * @author Takayuki Otake
     * @return Zend_From
     */
    private function _makeDbNameValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('');
        $nameValidators[] = array($notEmpty, true);

        return $nameValidators;
    }

    /**
     * データベースの接続ユーザー名のバリデータ
     *
     * @author Takayuki Otake
     * @return Zend_Form
     */
    private function _makeDbUserValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('');
        $userValidators[] = array($notEmpty);

        return $userValidators;
    }

    /**
     * データベース接続パスワードのバリデータ
     *
     * @author Takayuki Otake
     * @return Zend_Form
     */
    private function _makeDbPassValidators()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('');
        $passValidators[] = array($notEmpty);

        return $passValidators;
    }

    /**
     * データベーススキーマ取得
     * 
     * @author Takayuki Otake
     * @return String
     */
    function _getInitializeTablesSql()
    {

        $query = '';
        $commentFlag = false;
        $fp = fopen(APPLICATION_PATH . '/../sql/initialize_tables.sql', 'r');
        // MySQLスキーマのファイル内を走査しつつ、コメントは除外して抽出
        while ( $line = fgets($fp) ){
            // コメント行は無視する
            if ( preg_match("/\/\*/", $line) ){
                $commentFlag = true;
                continue;
            }
            if ( $commentFlag === true ){
                if ( preg_match("/\*\//", $line) ){
                    $commentFlag = false;
                }
                continue;
            }

            if ( preg_match("/^\-\-/", $line) ){
                continue;
            }


            $query .= $line;
            
        }
        fclose($fp);

        $querys = explode(";", $query);
        return $querys;
    }
}
