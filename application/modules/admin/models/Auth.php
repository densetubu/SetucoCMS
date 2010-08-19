<?php

/**
 * オペレータ認証関連モデル
 * 
 * @author Yuu Yamanaka
 */
class Admin_Model_Auth
{
    /**
     * ログイン処理を行う
     * 
     * @param string $loginId ログインID
     * @param string $password ログインパスワード
     * @return boolean ログインの成否
     */
    public function login($loginId, $password)
    {
        $adapter = new Setuco_Auth_Adapter_AdminModule(
                $loginId, $password);
        return Zend_Auth::getInstance()->authenticate($adapter)
                                       ->isValid();
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