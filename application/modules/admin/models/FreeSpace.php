<?php
/**
 * 管理側のフリースペース管理用のサービス
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
 * @since      File available since Release 1.5.0
 * @author     suzuki-mar
 */

/**
 * フリースペース管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     suzuki-mar
 */
class Admin_Model_FreeSpace extends Common_Model_FreeSpaceAbstract
{
    /**
     * フリースペースの情報を更新する
     *
     * @param array 更新するデータ
     * @return int 何件更新したのか
     * @throws update文に失敗したら例外を発生させる
     * @author suzuki-mar
     */
    public function updateFreeSpace($updateData)
    {
        //データは1件しかないないので、whereはいらない
        return $this->_freeSpaceDao->update($updateData, null);
    }

}
