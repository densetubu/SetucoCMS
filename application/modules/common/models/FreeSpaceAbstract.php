<?php
/**
 * フリースペース管理用サービス
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
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * タグ管理サービス
 *
 * @package    Common
 * @subpackage Model
 * @author     suzuki-mar
 */
abstract class Common_Model_FreeSpaceAbstract
{
    /**
     * タグDAO
     *
     * @var Common_Model_DbTable_FreeSpace
     */
    protected $_freeSpaceDao;

    /**
     * 指定したIDのタグ情報を取得する
     *
     * @param  int $id タグID
     * @return array タグ情報
     * @author charlesvineyard
     */
    public function findTag($id)
    {
        return $this->_tagDao->loadByPrimary($id);
    }

    /**
     * ページIDで指定されたページにつけられたタグの情報を返す。
     *
     * @param int $pageId タグを取得したいページのID
     * @return array 取得したタグ情報を格納した配列
     * @author akitsukada
     */
    public function findTagsByPageId($pageId)
    {
        return $this->_tagDao->loadTagByPageId($pageId);
    }

}
