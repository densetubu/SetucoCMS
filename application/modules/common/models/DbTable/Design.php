<?php

/**
 * designテーブルのDbTable(DAO)クラスです。
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
class Common_Model_DbTable_Design extends Setuco_Db_Table_Abstract
{

    /**
     * テーブル名
     *
     * @var String
     */
    protected $_name = 'design';

    /**
     * プライマリーキーのカラム名
     *
     * @var String
     */
    protected $_primary = 'id';

    /**
     * テーブルのalias名
     *
     * @var String
     */
    protected $_alias = 'd';

    /**
     * デザイン名を取得する
     *
     * @return string デザイン名
     * @author suzuki-mar
     */
    public function loadDesignName()
    {
        $select = $this->select($this->_name);

        $select->columns('design_name');

        $row = $this->fetchRow(null, "id DESC")->toArray();

        return $row['design_name'];
    }


    /**
     * 外部テーブルと結合したものを取得する
     * groupなどの必要な設定もしている
     *
     * @param Zend_Db_Select $select 結合するSELECTオブジェクト
     * @return void
     * @author suzuki-mar
     */
    protected function _joinPage(Zend_Db_Select &$select)
    {
        //テーブルを結合する 使用されていないものも取得する
        $select->joinLeft(array('p' => 'page'), "{$this->_alias}.id = p.category_id", array('title'));
        //結合するときはfalseにしないといけない
        $select->setIntegrityCheck(false);

        //categoryでグループ化
        $select->group("{$this->_alias}.id");
    }


    /**
     * すべてのカテゴリーを取得する
     *
     * @return array すべてのカテゴリー。なにもなければ空の配列。
     * @author suzuki-mar
     */
    public function loadAllCategories()
    {
        //初期設定をしているカテゴリーのSELECT文を取得する 外部結合する設定
        $select = $this->_initializeJoinSelect();

        return $this->fetchAll($select)->toArray();
    }

    /**
     * 指定したソートでカテゴリー一覧を取得します。
     *
     * @param  string $order カテゴリーを昇順か降順でソートするのか 文字列
     * @param  int    $pageNumber 取得するページ番号
     * @param  int    $limit 1ページあたり何件のデータを取得するのか
     * @return array  カテゴリー情報の一覧
     * @author suzuki-mar
     */
    public function loadCategories4Pager($order, $pageNumber, $limit)
    {
        //初期設定をしたSELECTオブジェクト
        $select = $this->_initializeSelect();

        $select->order("name {$order}")
               ->limitPage($pageNumber, $limit);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * 指定したカラム・ソートでカテゴリー一覧を取得します。
     *
     * @param string|array $selectColumns 取得するカラム
     * @param string $sortColumn 並べ替えるカラム名
     * @param string $order 並び順(asc or desc) デフォルトは asc
     * @return array カテゴリー情報の一覧
     * @author charlesvineyard
     */
    public function loadAllCategoriesSpecifiedColumns($selectColumns, $sortColumn, $order = 'ASC')
    {
        $select = $this->_initializeSelect()
                        ->from($this->_name, $selectColumns)
                        ->order("{$sortColumn} {$order}");
        return $this->fetchAll($select)->toArray();
    }

    /**
     * 指定の親カテゴリIdを持つカテゴリーを取得します。
     *
     * @param  int $parentId 親カテゴリID
     * @return array カテゴリー情報の一覧
     * @author charlesvineyard
     */
    public function loadCategoriesByParentId($parentId, $sortColumn, $order = 'ASC')
    {
        $select = $this->_initializeSelect()
                        ->where('parent_id = ?', $parentId)
                        ->order("{$sortColumn} {$order}");
        return $this->fetchAll($select)->toArray();
    }

    /**
     * 全ての有効なカテゴリーの件数を数えます。
     *
     * @return int 全てのカテゴリー件数
     * @author charlesvineyard
     */
    public function countAll()
    {
        return parent::countAll() - 1;    // PARENT_ROOT_IDの分減らす
    }

}

