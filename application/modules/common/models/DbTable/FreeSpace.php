<?php
/**
 * free_spaceテーブルのDbTable(DAO)クラスです。
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
 * @author     charlesvineyard
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      charlesvineyard
 */
class Common_Model_DbTable_FreeSpace extends Setuco_Db_Table_Abstract
{
    /**
     * テーブル名
     *
     * @var String
     */
    protected $_name = 'free_space';

    /**
     * プライマリーキーのカラム名
     *
     * @var String
     */
    protected $_primary = 'id';


    /**
     * フリースペースの内容を1件取得する
     *
     * @return string フリースペースの内容
     * @author suzuki-mar
     */
    public function loadNewContent()
    {
        $select = $this->select($this->_name);

        $select->columns('content');

        //更新した物が新しいものを取得する
        $select->order('id DESC');

        //指定した件数しか取得しない
        $select->limit(1);

        $results = $this->fetchRow($select)->toArray();
        $result = $results['content'];

        return $result;
    }

}

