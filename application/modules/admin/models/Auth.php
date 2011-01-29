<?php

/**
 * 認証サービスです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     Yuu Yamanaka, charlesvineyard
 */

/**
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     Yuu Yamanaka, charlesvineyard
 */
class Admin_Model_Auth
{

    /**
     * ユーザー認証インスタンス
     *
     * @var Zend_Auth
     */
    private $_authInstance;
    /**
     * ユーザー認証の実行結果
     *
     * @var Zend_Auth_Result
     */
    private $_authResult;
    /**
     * ユーザー認証アダプタークラス　
     * DBにアクセスするクラス
     *
     * @var Zend_Auth_Adapter_DbTable
     */
    private $_authAdapter;

    /**
     * 変数の初期設定をする
     *
     * @author suzuki-mar
     */
    public function  __construct()
    {
        $this->_authInstance = Zend_Auth::getInstance();
    }

    /**
     * ログイン処理を行う
     * 
     * @param  string $loginId  ログインID
     * @param  string $password ログインパスワード
     * @return void
     * @author suzuki-mar
     */
    public function login($loginId, $password)
    {
        $this->_authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $this->_authAdapter->setTableName('account')
                ->setIdentityColumn('login_id')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('sha1(?)');
        $this->_authAdapter->setIdentity($loginId)
                ->setCredential($password);

        $this->_authResult = $this->_authInstance->authenticate($this->_authAdapter);
    }

    /**
     * ログインに成功したか
     *
     * @return boolean ログイン認証に成功したか
     * @author suzuki-mar
     */
    public function isLoginSuccess()
    {
        return $this->_authResult->isValid();
    }

    /**
     * ユーザー情報を書き込む
     *
     * @return void
     * @author suzuki-mar
     */
    public function setAccountInfos()
    {
        $storage = $this->_authInstance->getStorage();
        $storage->write($this->_authAdapter->getResultRowObject(null, 'password'));
    }

    /**
     * ユーザー情報を取得する
     *
     * @return array ユーザー情報
     * @author suzuki-mar
     */
    public function getAccountInfos()
    {
        $storage = $this->_authInstance->getStorage();
        $userInfoInstance = $storage->read();
        return get_object_vars($userInfoInstance);
    }

    /**
     * ログインしているか
     *
     * @return boolean ログインしているか
     * @author suzuki-mar
     */
    public function isLoggedIn()
    {
        return $this->_authInstance->hasIdentity();
    }

    /**
     * ログアウト処理を行う
     * 
     * @return void
     */
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
    }

}