<?php
/**
 * 閲覧側のページ情報管理用サービス
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
 * @package    Default
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar akitsukada
 */

/**
 * ページ情報管理クラス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar akitsukada
 */
class Default_Model_Page extends Common_Model_PageAbstract
{

    /**
     * 初期設定をする
     *
     * @author suzuki_mar
     */
    public function __construct()
    {
        $this->_pageDao = new Common_Model_DbTable_Page();
        $this->_tagDao = new Common_Model_DbTable_Tag();
    }

    /**
     * 最新の編集したページを取得する
     *
     * @param int 何件のデータを取得するのか　標準は10件　取得できない場合はfalseを返す
     * @author suzuki-mar
     */
    public function findLastUpdatedPages($limit)
    {
        //更新順のページを取得する
        $result = $this->_pageDao->loadLastUpdatePages($limit);

        //からならfalseを返す
        if (empty($result)) {
            $result = false;
        }

        return $result;
    }

    /**
     * ページを登録しているか
     *
     * @return boolean ページを登録している
     * @author suzuki-mar
     */
    public function isEntryExists()
    {
        $count = $this->countPagesByStatus(Setuco_Data_Constant_Page::STATUS_RELEASE);
        $result = ($count > 0);
        return $result;
    }

    /**
     * 未分類のカテゴリーのページを登録しているか
     *
     * @return boolean 未分離のカテゴリーのページを登録しているか
     * @author suzuki-mar
     */
    public function isEntryUncategorizedPage()
    {
        $count = $this->_pageDao->countUncategorizedPage('open');
        return ($count > 0);
    }

    /**
     * タグを指定してページを取得する
     *
     * @param int $tagId 取得したいタグID
     * @return array 該当するタグがつけられたページのデータを格納した配列
     * @author akitsukada
     */
    public function findPagesByTagId($tagId, $currentPage, $limit)
    {
        return $this->_pageDao->loadPagesByTagId4Pager($tagId, $currentPage, $limit);
    }

    /**
     * 指定したタグIDのタグがつけられたページの数を取得する
     *
     * @param int $tagId カウントしたいタグのID
     * @return int 該当するページ数
     * @author akitsukada
     */
    public function countPagesByTagId($tagId)
    {
        return count($this->_pageDao->loadPagesByTagId4Pager($tagId));
    }
}

