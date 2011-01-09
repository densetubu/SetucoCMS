<?php
/**
 * tagテーブルのDbTable(DAO)クラスです。
 *
 * LICENSE: ライセンスに関する情報
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
 * @package     Common_Model
 * @subpackage  DbTable
 * @author      charlesvineyard suzuki-mar
 */
class Common_Model_DbTable_Tag extends Zend_Db_Table_Abstract
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
     * @param int $page 現在のページ番号
     * @param int $limit 1ページあたり何件のデータを取得するのか
     * @return array タグ情報の配列
     * @author charlesvineyard
     */
    public function loadSortedTags($order, $page, $limit)
    {
        $select = $this->select()->limitPage($page, $limit)->order("name {$order}");
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
     *
     * @param string $tagName 検索したいキーワード
     * @return array|null 合致するタグのIDを格納した配列。該当するタグがなければnull。
     */
    public function loadTagIdsByTagName($tagName)
    {
        $select = $this->select()->from(array('t' => $this->_name), 'id');
        $select->where('name LIKE ?', "%{$tagName}%");

        $rowset = $this->fetchAll($select)->toArray();

        if (count($rowset) == 0) {
            return null;
        }

        $tagIds = array();
        foreach ($rowset as $cnt => $tag) {
            array_push($tagIds, (int)$tag['id']);
        }
        return $tagIds;
    }

    /**
     * タグ名を検索し、該当するタグのIDを返す。
     *
     * @param string $tagName 検索したいタグ名
     * @return array|null タグID。該当するタグがなければnull。
     */
    public function loadTagIdByTagName($tagName)
    {
        $select = $this->select()->from(array('t' => $this->_name), 'id');
        $select->where('name = ?', $tagName);
        $result = $this->fetchRow($select);
        if (count($result) == 0) {
            return null;
        }
        $result = $result->toArray();
        return $result['id'];
    }

}