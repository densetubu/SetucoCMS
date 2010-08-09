<?php
/**
 * 管理画面用の認証管理アダプタ
 * 
 * @author Yuu Yamanaka
 */
class Setuco_Auth_Adapter_AdminModule implements Zend_Auth_Adapter_Interface
{
    /**
     * ログインID
     * 
     * @var string
     */
    private $_loginId;
    
    /**
     * ログインパスワード
     * 
     * @var string
     */
    private $_password;
    
    /**
     * 認証用のアカウント情報をセットする
     * 
     * @param string $loginId
     * @param string $password
     */
    public function __construct($loginId, $password)
    {
        $this->_loginId = $loginId;
        $this->_password = $password;
    }
    
    // TODO DBを用いた認証処理
    public function authenticate()
    {
        if (($this->_loginId == 'admin') &&
            ($this->_password == 'password')) {
            $code = Zend_Auth_Result::SUCCESS;
        } else {
            $code = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
        }
        
        $result = new Zend_Auth_Result($code, $this->_loginId);
        return $result;
    }
}