<?php
/**
 * アカウントに関するサービスです。
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
 * @author     charlesvineyard
 */

/**
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     charlesvineyard
 */
class Admin_Model_Account
{
    /**
     * アカウントDAO
     *
     * @var Common_Model_DbTable_Account
     */
    private $_accountDao;

    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_accountDao = new Common_Model_DbTable_Account();
    }

    /**
     * アカウント情報をロードします。
     *
     * @return array アカウント情報の配列
     * @author charlesvineyard
     */
    public function findAccountByLoginId($loginId)
    {
        return $this->_accountDao->loadAccountByLoginId($loginId);
    }

    /**
     * アカウントIDとニックネームのセットを取得する。
     *
     * @return array キー:アカウントID、値:ニックネームの配列
     * @author charlesvineyard
     */
    public function findAllAccountIdAndNicknameSet()
    {
        $result = $this->_accountDao->loadAllAccounts(array('id', 'nickname'), 'nickname');
        $idNameSet = array();
        foreach ($result as $row) {
            $idNameSet[$row['id']] = $row['nickname'];
        }
        return $idNameSet;
    }

    /**
     * 指定したパスワードが同じかを調べる
     *
     * @param string $password 同じかをチェックするパスワード
     * @param string $loginId パスワードが同じかを調べるユーザーのログインID
     * @return boolean 同じかどうか
     */
    public function isSamePassword($password, $loginId)
    {
        $accountInfos = $this->findAccountByLoginId($loginId);
        $hashPassword = hash('sha1', $password);

        return ($hashPassword === $accountInfos['password']);
    }

    /**
     * ニックネーム情報を変更する
     *
     * @param string $loginId ニックネームを変更するログインID
     * @param string $nickname 変更するニックネーム
     * @return int 変更した件数
     * @author ErinaMikami
     */
    public function updateNickname($loginId, $nickname)
    {
      $where = $this->_accountDao->getAdapter()->quoteInto('login_id = ?', $loginId);
      $updateParams['nickname'] = $nickname;
      return $this->_accountDao->update($updateParams, $where);
    }

    /**
     * パスワード情報を変更する
     *
     * @param string $password 変更するパスワード
     * @param string $loginId パスワードを変更するID
     * @return int 変更した件数
     * @author suzuki-mar
     */
    public function updatePassword($password, $loginId)
    {
      $where = $this->_accountDao->getAdapter()->quoteInto('login_id = ?', $loginId);
      $updateParams['password'] = sha1($password);
      return $this->_accountDao->update($updateParams, $where);

    }
}

