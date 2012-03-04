<?php

/**
 * ページ情報管理用サービス
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
 * @author     charlesvineyard akitsukada
 */

/**
 * ページ情報管理クラス
 *
 * @package    Common
 * @subpackage Model
 * @author     charlesvineyard akitsukada
 */
abstract class Common_Model_PageAbstract
{

    /**
     * キーワード検索のデフォルト検索対象カラム
     *
     * @var array
     */
    private $_searchTargetColumns = array('title', 'contents', 'outline', 'tag');
    /**
     * PageテーブルのDAO
     *
     * @var Common_Model_DbTable_Page
     */
    protected $_pageDao;
    /**
     * TagテーブルのDAO
     *
     * @var Common_Model_DbTable_Tag
     */
    protected $_tagDao;

    /**
     * ページ情報を取得する
     *
     * @param int $id  ページID
     * @return array ページ情報
     * @author charlesvineyard
     */
    public function findPage($id)
    {
        return $this->_pageDao->loadByPrimary($id);
    }

    
    /**
     * ページのキーワード検索を行う。検索対象はタイトル、本文、概要、タグ。（ページネータ対応）
     *
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return array 検索結果を格納した配列
     * @author akitsukada suzuki-mar
     * @todo 取得するカラムを動的にしたい。今は全取得。
     */
    public function searchPages(Common_Model_Page_Param $paramIns)
    {
        if (!is_null($paramIns->getTargetColumns())) {
            $params['_targetColumns'] = $this->_searchTargetColumns;
        }

        $params['_tagIds'] = $this->_searchTagIdsByKeyword($paramIns->getKeyword());
        $paramIns->setDaoParams($params);

        return $this->_pageDao->loadPagesByKeyword4Pager($paramIns);
    }

    /**
     * ページのキーワード検索結果の合計数を求める。
     *
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return int 該当するページの合計数
     * @author akitsukada suzuki-mar
     */
    public function countPagesByKeyword(Common_Model_Page_Param $paramIns)
    {
        if (!is_null($paramIns->getTargetColumns())) {
            $params['_targetColumns'] = $this->_searchTargetColumns;
        }

        $params['_tagIds'] = $this->_searchTagIdsByKeyword($paramIns->getKeyword());
        $paramIns->setDaoParams($params);

        return (int) ($this->_pageDao->countPagesByKeyword($paramIns));
    }

    /**
     * タグ名をキーワード検索し、該当するタグのIDを返す
     *
     * @param string $keyword 検索したいキーワード
     * @return array|null 該当するタグのIDを格納した配列
     * @author akitsukada
     */
    protected function _searchTagIdsByKeyword($keyword)
    {
        return $this->_tagDao->loadTagIdsByKeyword($keyword);
    }

    /**
     * カテゴリを指定してページを取得する（ページネータ対応）
     *
     * @param int $categoryId 取得したいカテゴリのID
     * @author akitsukada
     * @return array 該当するカテゴリのページデータを格納した配列
     */
    public function findPagesByCategoryId($categoryId, $status = null, $currentPage = null, $limit = null, $sortColumn = 'update_date', $order = 'DESC')
    {
        return $this->_pageDao->loadPagesByCategoryId4Pager($categoryId, $status, $currentPage, $limit, $sortColumn, $order);
    }

    /**
     * 指定したカテゴリに属するページの数を取得する
     *
     * @param int $categoryId ページ数を取得したいカテゴリのID
     * @return int 該当するページの数
     * @author akitsukada
     */
    public function countPagesByCategoryId($categoryId, $status = null)
    {
        return count($this->_pageDao->loadPagesByCategoryId4Pager($categoryId, $status));
    }


    /**
     * 状態と作成年月をに合ったページを数えます。
     *
     * @param int $status ページの状態（Setuco_Data_Constant_Page::STATUS_*）
     *                     指定しなければ全ての状態のものを数えます。
     * @return int ページ数
     * @author charlesvineyard
     */
    public function countPagesByStatus($status = null)
    {
        return $this->_pageDao->countPagesByStatusAndCreateDateSpan($status);
    }

    /**
     * 全てのページを数えます。
     *
     * @return int ページ数
     * @author charlesvineyard
     */
    public function countAllPages()
    {
        return $this->_pageDao->countAll();
    }

}

