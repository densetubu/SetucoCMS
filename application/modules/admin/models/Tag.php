<?php
/**
 * 管理側のタグ管理用サービス
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
 * @author     saniker10, suzuki-mar
 */

/**
 * タグ管理サービス
 *
 * @package    Admin
 * @subpackage Model
 * @author     saniker10, suzuki-mar
 */
class Admin_Model_Tag extends Common_Model_TagAbstract
{
    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_tagDao = new Common_Model_DbTable_Tag();
    }

    /**
     * タグ情報を取得する。タグ名でソートします。
     *
     * @param  string $order       asc か　desc
     * @param  int    $pageNumber  ページ番号(オフセットカウント)
     * @param  int    $limit       一つのページに出力する数(オフセット)
     * @return array タグ情報の一覧
     * @author saniker10, suzuki-mar, charlesvineyard
     */
    public function findTags($order, $pageNumber, $limit)
    {
        return $this->_tagDao->loadTags4Pager($order, $pageNumber, $limit);
    }

    /**
     * すべてのタグを数えます。
     *
     * @return int すべてのタグの個数
     * @author charlesvineyard
     */
    public function countAllTags()
    {
        return $this->_tagDao->countAll();
    }

    /**
     * タグを登録する。
     *
     * @param  string $name タグ名
     * @return void
     * @author saniker10, suzuki-mar
     */
    public function registTag($name)
    {
        $this->_tagDao->insert(array('name' => $name));
    }

    /**
     * タグを更新する。
     *
     * @param  $id   タグID
     * @param  $name タグ名
     * @return bool 更新できたら true。該当データがなかったら false。
     * @author charlesvineyard
     */
    public function updateTag($id, $name)
    {
        return $this->_tagDao->updateByPrimary(array('name' => $name), $id);
    }

    /**
     * タグを削除する。
     *
     * @param  $id   タグID
     * @return bool 更新できたら true。該当データがなかったら false。
     * @author charlesvineyard
     */
    public function deleteTag($id)
    {
        return $this->_tagDao->deleteByPrimary($id);
    }
}
