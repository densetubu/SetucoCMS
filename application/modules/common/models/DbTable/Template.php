<?php

/**
 * templateテーブルのDbTable(DAO)クラスです。
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
 * @author     suzuki-mar
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      suzuki-mar
 */
class Common_Model_DbTable_Template extends Setuco_Db_Table_Abstract
{

    /**
     * テーブル名
     *
     * @var string
     */
    protected $_name = 'template';

    /**
     * プライマリキーのカラム名
     *
     * @var string
     */
    protected $_primary = 'id';


    /**
     * 指定したアカウントIDのファイル名一覧を取得する
     *
     * @param int $accountId ファイル名一覧を取得するアカウントID
     * @return array 指定したアカウントのファイル名一覧
     * @author suzuki-mar
     */
    public function findFileNamesByAccountId($accountId)
    {
        $select = $this->select()
            ->from($this->_name, 'file_name')
            ->where('account_id = ?', $accountId);

        foreach ($this->fetchAll($select)->toArray() as $row) {
            $result[] = $row['file_name'];
        }

        return $result;
        
    }

    /**
     * 全てのレコードを配列で返す
     *
     * @return array 全てのレコードの配列
     * @author suzuki-mar
     */
    public function loadAllTemplates()
    {
      return $this->fetchAll()->toArray();
    }

 

}
