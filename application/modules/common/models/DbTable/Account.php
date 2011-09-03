<?php
/**
 * accountテーブルのDbTable(DAO)クラスです。
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
 * @package    Common
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     mitchang
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      mitchang
 */
class Common_Model_DbTable_Account extends Setuco_Db_Table_Abstract
{
    /**
     *     テーブル名
     *
     *    @var string
     */
    protected $_name = 'account';

    /**
     *    プライマリキーのカラム名
     *
     *    @var string
     */
    protected $_primary = 'id';

    /**
     * ログインIDからアカウント1件を取得します。
     *
     * @param  string $loginId ログインID
     * @return array アカウント情報
     * @author charlesvineyard
     */
    public function loadAccountByLoginId($loginId)
    {
        $select = $this->select()->where('login_id = ?', $loginId);
        $rowset = $this->fetchAll($select);
        if ($rowset->count() == 0) {
            return null;
        }
        return $rowset->current()->toArray();
    }


    /**
     * 指定したカラム・ソートで全てのアカウント一覧を取得します。
     *
     * @param string|array $selectColumns 取得するカラム
     * @param string $sortColumn 並べ替えるカラム名
     * @param string $order 並び順(asc or desc) デフォルトは asc
     * @return array アカウント情報の一覧
     * @author charlesvineyard
     */
    public function loadAllAccounts($selectColumns, $sortColumn, $order = 'ASC')
    {
        $select = $this->select()
                ->from($this->_name, $selectColumns)
                ->order("{$sortColumn} {$order}");
        return $this->fetchAll($select)->toArray();
    }

    

}
