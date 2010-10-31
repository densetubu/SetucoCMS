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
     * ログイン処理を行う
     * 
     * @param  string $loginId  ログインID
     * @param  string $password ログインパスワード
     * @return boolean ログインの成否 
     */
    public function login($loginId, $password)
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('account')
                    ->setIdentityColumn('login_id')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('sha1(?)');
        $authAdapter->setIdentity($loginId)
                    ->setCredential($password);
        $auth = Zend_Auth::getInstance();
        return $auth->authenticate($authAdapter)->isValid();
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