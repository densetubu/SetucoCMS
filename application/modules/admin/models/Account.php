<?php
/**
 * アカウントに関するサービスです。
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
    * アカウントを新規追加する
    *
    * @author kkyouhei 
    */
    public function registAccount($registData)
    {
        $registData['password'] = hash('sha1', $registData['password']);
        return $this->_accountDao->insert($registData);
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
     * ログインIDとニックネームのセットを取得する。
     *
     * @return array キー:ログインID、値:ニックネームの配列
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
    * アカウントレコードを必要があればソートして結果を返す
    *
    * @param string array どのカラムを取得するか
    * @param string array $order 並び順 
    * @param string $pageNumber 取得するページ番号 
    * @param string $order 1ページあたり何件のデータを取得するのか 
    * @return array アカウント情報の一覧
    * @author kkyouhei
    */
    public function findSortAllAcounts($selectColumns, $sortColumn, $order, $pageNumber, $limit)
    {
        return $this->_accountDao->loadAccounts4Pager($selectColumns, $sortColumn, $order, $pageNumber, $limit);
    }

    /**
    * アカウント数を取得するメソッド
    *
    * @return int 全てのアカウント個数
    * @author kkyouhei
    */
    public function countAllAccounts()
    {
        return $this->_accountDao->countAll();
    }

    /* 
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

