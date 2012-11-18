<?php
/**
 * tagテーブルのDbTable(DAO)クラスです。
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
 * @author     charlesvineyard suzuki-mar
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      charlesvineyard suzuki-mar
 */
class Common_Model_DbTable_Tag extends Setuco_Db_Table_Abstract
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $_name = 'tag';

    /**
     * プライマリーキーのカラム名
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * タグクラウド情報を取得する
     *
     * @return array タグクラウドの情報を取得する
     * @author suzuki-mar
     */
    public function loadTagCloudInfos()
    {
        //タグ名とどれぐらい使用されているかをカウントする
        $select = $this->select()->from(array('t' => $this->_name), array('id', 'name'));

        //テーブルを結合する
        $select->join(array('pt' => 'page_tag'), 't.id = pt.tag_id', array('count' => 'COUNT(pt.tag_id)'));
        $select->join(array('p' => 'page'), 'p.id = pt.page_id', array('update_date', 'create_date'));

        //結合するときはfalseにしないといけない
        $select->setIntegrityCheck(false);

        //公開しているものしか取得しない
        $select->where('p.status = ?', Common_Model_DbTable_Page::STATUS_OPEN);
        //tagごとにカウントする
        $select->group('pt.tag_id');

        //カウントが多い順
        $select->order('count DESC');


        $result = $this->fetchAll($select)->toArray();

        return $result;
    }

    /**
     * 指定した並び順でタグ一覧を取得します。
     *
     * @param string|array $order 並び順
     * @param int $pageNumber 取得するページ番号
     * @param int $limit 1ページあたり何件のデータを取得するのか
     * @return array タグ情報の配列
     * @author charlesvineyard
     */
    public function loadTags4Pager($order, $pageNumber, $limit)
    {
        $select = $this->select()->limitPage($pageNumber, $limit)->order("name {$order}");
        return $this->fetchAll($select)->toArray();
    }

    /**
     * ページのIDを指定して、そのページにつけられたタグの情報を取得して返す。
     *
     * @param int $pageId タグを取得したいページのID
     * @return array 取得したタグ情報を格納した配列
     * @author akitsukada
     */
    public function loadTagByPageId($pageId)
    {
        $select = $this->select()->from(array('t' => $this->_name));

        $select->join(array('pt' => 'page_tag'), 't.id = pt.tag_id');
        $select->setIntegrityCheck(false);

        $select->where('pt.page_id = ?', $pageId);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * タグ名をキーワード検索し、該当するタグのIDを返す。
     * 複数キーワードに対応している
     *
     * @param string $tagName 検索したいキーワード
     * @return array|null 合致するタグのIDを格納した配列。該当するタグがなければ空の配列。
     * @author akitsukada
     */
    public function loadTagIdsByKeyword($keyword)
    {
        $keyword = $this->escapeLikeString($keyword);
        $select = $this->select()->from(array('t' => $this->_name), 'id');
        $columnExpr = $this->getBsReplacedExpression('name');

        $where = Setuco_Sql_Generator::createMultiLike4Keyword($keyword, $columnExpr, 'OR');
        $bind  = Setuco_Sql_Generator::createMultiLikeTargets($keyword);
        
        $select->where($where);
        $select->bind($bind);


        $rowset = $this->fetchAll($select);
        if ($rowset->count() == 0) {
            return array();
        }
        
        $tagIds = array();
        foreach ($rowset->toArray() as $cnt => $tag) {
            array_push($tagIds, (int)$tag['id']);
        }
        
        return $tagIds;
    }

    /**
     * タグ名を検索し、該当するタグのIDを返す。
     *
     * @param string $tagName 検索したいタグ名
     * @return int|null タグID。該当するタグがなければnull。
     * @author charlesvineyard
     */
    public function loadTagIdByTagName($tagName)
    {
        $select = $this->select()->from(array('t' => $this->_name), 'id');
        $select->where('name = ?', $tagName);

        $row = $this->fetchRow($select);

        if (is_null($row)) {
            return null;
        }
        return (int) array_pop($row->toArray());
    }

}