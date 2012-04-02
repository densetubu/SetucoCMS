<?php

/**
 * インストーラのコントローラ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Install
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @since      File available since Release 0.1.0
 * @author     Takayuki Otake suzuki-mar
 * @todo       フォーム処理をZenfFormに置き換える
 */

/**
 * Description of InstallController
 *
 * @author Takayuki Otake
 */
class Install_InstallController
    extends Setuco_Controller_Action_InstallAbstract
{

    /**
     *データベースハンドラ
     *
     * @var 
     */
    protected $dbh;

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
     * 入力フォームから初期設定をするアクション
     *
     * @author Takayuki Otake
     * @return void
     */
    public function indexAction()
    {
        $inputValues = $this->_getAllParams();

        $template = 'index';
        if ($this->_request->isPost()) {
            $this->_setSession($inputValues);
            if (!$this->_initializeFormValidator->isValid($inputValues)) {
                $template = 'index';
            } else {
                if ($this->_getParam('submit')) {
                    $template = 'confirm';
                } else if ($this->_getParam('commit')) {
                    $this->_initialize($inputValues);
                    $this->_helper->redirector('finish', 'install', null); 
                }
            }
            $inputValues = $this->_initializeFormValidator->getValues();
            $this->view->errorForm = $this->_initializeFormValidator;
        } else {
            $template = 'index';
        }

        if ($template == 'index') {
            $defaultValues = $this->_getDefaultValues();
            foreach ($defaultValues as $key => $value) {
                if (empty($inputValues[$key])) {
                    $inputValues[$key] = $defaultValues[$key];
                }
            }
            unset($defaultValues);
        }

        $this->view->inputValues = $inputValues;
        return $this->render($template);

    }

    /**
     * データベースと設定ファイルの初期化
     *
     * @param array $validData 設定ファイルとデータベースの設定パラメータ
     * @author Takayuki Otake
     * @return void
     */
    public function _initialize($validData)
    {
        $validData = $this->_initializeFormValidator->getValues();
        if (preg_match("/^http(s):\/\//", $validData['site_url']) === false) {
            $validData['site_url'] .= 'http://';
        }

        $fhr = fopen(APPLICATION_PATH . '/configs/application-sample.ini', 'r');
        $fhw = fopen(APPLICATION_PATH . '/configs/application.ini', 'w');
        while ($line = fgets($fhr)){
            $key = '';
            if (preg_match("/resources\.db\.params\.(.*?)(\s+)?\=(\s+)?\"(.*?)\"/", $line, $matches)){
                if ($matches[1] === 'host'){
                    $key = 'db_host';
                } elseif ($matches[1] === 'username') {
                    $key = 'db_user';
                } elseif ($matches[1] === 'password') {
                    $key = 'db_pass';
                } elseif ($matches[1] === 'dbname') {
                    $key = 'db_name';
                }

                if (!empty($key)){
                    $line = str_replace($matches[4], $validData[$key], $line);
                }
            }
            fwrite($fhw, $line);
        }
        fclose($fhr);
        fclose($fhw);

        try{
            $this->_dbConnect($validData);
            $this->dbh->query("SET NAMES utf8");
            $querys = $this->_getInitializeTablesSql();
            foreach ($querys as $query){
                if (!empty($query)){
                    $this->dbh->query($query);
                }
            }

            $sth = $this->dbh->prepare('UPDATE site SET name = ?, url = ?, comment = ? WHERE id = ?');
            $sth->execute(array($validData['site_name'], $validData['site_url'], $validData['site_comment'], 1));

            $sql = "UPDATE account set login_id = '{$validData['account_id']}', password = SHA1('{$validData['account_pass']}')";
            $this->dbh->query($sql);
        } catch (Zend_Exception $pe) {
            $dbh = null;
            throw new Setuco_Exception('update文の実行に失敗しました。' . $pe->getMessage());
        } catch (Exception $e) {
            throw new Setuco_Exception('エラーが発生しました。', $e->getMessage());
        }
        $dbh = null;


        $this->_helper->redirector('finish', 'install', null);
    }

    /**
     * セットアップ終了アクション
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
     * データベースに接続する関数
     *
     * @author Takayuki Otake
     * @return boolean
     */
    private function _dbConnect($params)
    {
        try {
            $this->dbh = new PDO("mysql:host={$params['db_host']}; dbname={$params['db_name']}",
                    $params['db_user'], $params['db_pass']);
        } catch (PDOException $e) {
            return false;
        }
        return true;
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
            } else {
                $this->_session->$key = $values[$key];
            }
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

        $adminAccountIdElement = new Zend_Form_Element_Text('account_id', array(
                    'id' => 'account_id',
                    'required' => 'true',
                    'validators' => $this->_makeAdminAccountIdValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($adminAccountIdElement);

        $adminAccountPassElement = new Zend_Form_Element_Text('account_pass', array(
                    'id' => 'account_pass',
                    'required' => 'true',
                    'validators' => $this->_makeAdminAccountPasswordValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($adminAccountPassElement);

        $adminAccountPassCheckElement = new Zend_Form_Element_Text('account_pass_check', array(
                    'id' => 'account_pass_check',
                    'required' => 'true',
                    'validators' => $this->_makeAdminAccountPasswordValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($adminAccountPassCheckElement);

        $siteNameElement = new Zend_Form_Element_Text('site_name', array(
                    'id' => 'site_name',
                    'required' => 'true',
                    'validators' => $this->_makeSiteNameValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($siteNameElement);

        $siteCommentElement = new Zend_Form_Element_Text('site_comment', array(
                    'id' => 'site_comment',
                    'required' => 'true',
                    'validators' => $this->_makeSiteCommentValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($siteCommentElement);

        $siteUrlElement = new Zend_Form_Element_Text('site_url', array(
                    'id' => 'site_url',
                    'required' => 'true',
                    'validators' => $this->_makeSiteUrlValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($siteUrlElement);

        $dbHostElement = new Zend_Form_Element_Text('db_host', array(
                    'id' => 'db_host',
                    'required' => 'true',
                    'validators' => $this->_makeDbHostValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($dbHostElement);

        $dbNameElement = new Zend_Form_Element_Text('db_name', array(
                    'id' => 'db_name',
                    'required' => 'true',
                    'validators' => $this->_makeDbNameValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($dbNameElement);

        $dbUserElement = new Zend_Form_Element_Text('db_user', array(
                    'id' => 'db_user',
                    'required' => 'true',
                    'validators' => $this->_makeDbUserValidators(),
                    'filters' => array('StringTrim')
                    ));
        $form->addElement($dbUserElement);

        $dbPassElement = new Zend_Form_Element_Text('db_pass', array(
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
        $stringLength->setMessage('サイト名は%max%文字以下で入力してください。');
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
        $stringLength->setMessage('サイトの説明は%max%文字以下で入力しだください。');
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
        $notEmpty->setMessage('管理者アカウントのログインIDを入力してください');
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
        $notEmpty->setMessage('管理者アカウントのパスワードを入力してください。');
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
                    'check_key' => 'account_pass_check'
                    )
                );
        $confirmCheck->setMessage('パスワードとパスワード確認が一致しません。');
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
        $notEmpty->setMessage('接続するデータベースのアドレスを入力してください。');
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
        $notEmpty->setMessage('接続するデータベースのアドレスを入力してください。');
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
        $comment_flg = false;
        $fp = fopen(APPLICATION_PATH . '/../sql/initialize_tables.sql', 'r');
        while ( $line = fgets($fp) ){
            // MySQLスキーマのファイル内を走査しつつ、コメントは除外して抽出
            if ( $comment_flg === true ){
                if ( preg_match("/\*\//", $line) ){
                    $comment_flg = false;
                }
            } else {
                if ( preg_match("/\/\*/", $line) ){
                    $comment_flg = true;
                } elseif ( preg_match("/^\-\-/", $line) ){
                    // コメント行は無視する
                } else {
                    $query .= $line;
                }
            }
        }
        fclose($fp);

        $querys = explode(";", $query);
        return $querys;
    }
}
