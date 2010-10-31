<?php
/**
 * pageテーブルのDbTable(DAO)クラスです。
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
 * @author     mitchang
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      mitchang
 */
class Common_Model_DbTable_Page extends Zend_Db_Table_Abstract
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $_name = 'page';
    /**
     * プライマリーキーのカラム名
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * 記事の状態　公開
     */
    const STATUS_OPEN = 1;

    /**
     * 記事の状態　下書き
     */
    const STATUS_DRAFT = 0;

    /**
     * 新着記事を取得する
     *
     * @param int $getPageCount 何件の記事を取得するのか
     * @author suzuki-mar
     */
    public function findLastUpdatePages($getPageCount)
    {
        $select = $this->select();
        
        //公開しているものしか取得しない
        $select->where('status = ?', self::STATUS_OPEN);

        //編集日時が最新順にソートする
        $select->order('update_date DESC');

        //指定した件数しか取得しない
        $select->limit($getPageCount);

        $result = $this->fetchAll($select)->toArray();
        return $result;
    }

    /**
     * 最近作成(公開)したページを取得する
     *
     * @param  int     $getPageCount   何件の記事を取得するのか
     * @param  boolean $isJoinCategory カテゴリーテーブルを結合するならtrue。デフォルトはしない
     * @param  boolean $isJoinAccount  アカウントテーブルを結合するならtrue。 デフォルトはしない
     * @author charlesvineyard
     */
    public function findLastCreatedPages($getPageCount, $isJoinCategory = false, $isJoinAccount = false)
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        if ($isJoinCategory) {
            $select->setIntegrityCheck(false)
                   ->join('category', 'page.category_id = category.id');
        }
        if ($isJoinCategory) {
            $select->setIntegrityCheck(false)
                   ->join('account', 'page.account_id = account.id');
        }
        $select->where('status = ?', self::STATUS_OPEN)
               ->order('create_date desc')
               ->limit($getPageCount);
        return $this->fetchAll($select)->toArray();
    }    
    
    /**
     * ページを数えます。
     *
     * @param  int $status ページの状態（Setuco_Data_Constant_Page::STATUS_*）
     *                     指定しなければ全ての状態のものを数えます。
     * @param  int $createDateStart  作成日時の最小値(この値自体を含む)
     * @param  int $createDateEnd    作成日時の最大値(この値自体を含まない)
     * @return int ページ数
     * @author charlesvineyard
     */
    public function countPages($status = null, $createDateStart = null, $createDateEnd = null)
    {
        $select = $this->select();
        if (! is_null($status)) {
            $select->where('status = ?', $status);
        }
        if (! is_null($createDateStart)) {
            $select->where('create_date >= ?', $createDateStart);
        }                
        if (! is_null($createDateEnd)) {
            $select->where('create_date < ?', $createDateEnd);
        }
        return $this->fetchAll($select)->count();
    }
    
    /**
     * カテゴリを指定して記事を取得する
     *
     * @param int $catId 取得したいカテゴリのID
     * @param int $currentPage ページネータの何ページ目を表示するか
     * @param int $limig １ページに表示する記事数
     * @author akitsukada
     * @return
     */
    public function findPagesByCategoryId($catId, $currentPage = null, $limit = null)
    {
        $select = $this->select();

        $select->from(array('p' => $this->_name));

        //投稿者名取得のためJOIN
        $select->joinLeft(array('a' => 'account'), 'a.id = p.account_id', array('account_name' => 'a.nickname'));
        $select->setIntegrityCheck(false);

        //公開している記事のみ取得する
        $select->where('status = ?', self::STATUS_OPEN);

        //指定されたカテゴリの記事のみ取得する
        if (is_null($catId)) {
            $select->where('category_id is null');
        } else {
            $select->where('category_id = ?', $catId);
        }

        //編集日時の降順にソートする
        $select->order('update_date DESC');

        if (!is_null($currentPage) && !is_null($limit)) {
            //ページネータの設定（何ページ目を表示するか、何件ずつ表示するか）
            $select->limitPage($currentPage, $limit);
        }

        return $this->fetchAll($select);

    }

    /**
     * タグIDを指定して記事を取得する（ページネータ対応）
     *
     * @param int $tagId 取得したいタグのID
     * @param int $currentPage ページネータで何ページ目を表示するか。省略すると
     */
    public function findPagesByTagId($tagId, $currentPage = null, $limit = null)
    {
        $select = $this->select();

        $select->from(array(
            'p'  => $this->_name
        ));


        //編集日時の降順にソートする
        $select->order('p.update_date DESC');

        $select->join(array('pt' => 'page_tag'), 'pt.page_id = p.id');

        $select->setIntegrityCheck(false);

        $select->where('p.status = ?', self::STATUS_OPEN);
        $select->where('pt.tag_id = ?', $tagId);

        if (!is_null($currentPage) && !is_null($limit)) {
            //ページネータの設定（何ページ目を表示するか、何件ずつ表示するか）
            $select->limitPage($currentPage, $limit);
        }

        return $this->fetchAll($select);
        
    }

    public function searchPage($keyword, $currentPage, $limit)
    {
        $select = $this->select();
        $select->from(
            array('p' => $this->_name),
            array(
                'id' => 'id',
                'title' => 'title',
                'contents',
                'update_date'
            )
        );

        $select->order('p.update_date DESC');
        $select->joinLeft(array('pt' => 'page_tag'), 'pt.page_id = p.id', array());
        $select->join(array('c' => 'category'), 'c.id = p.category_id', array('category_name' => 'c.name'));
        $select->join(array('t' => 'tag'), 't.id = pt.tag_id', array('tag_name' => 't.name'));
        $select->setIntegrityCheck(false);

        $select->orwhere('p.title LIKE ?', "%{$keyword}%");
        $select->orwhere('p.contents LIKE ?', "%{$keyword}%");
        $select->orwhere('p.outline LIKE ?', "%{$keyword}%");
        $select->orwhere('p.title LIKE ?', "%{$keyword}%");

        $select->limitPage($currentPage, $limit);

        return $this->fetchAll($select);

    }

    public function countPagesByKeyword($keyword)
    {
        $select = $this->select();
        $select->from(
            array('p' => $this->_name),
            array('page_count' => 'COUNT(DISTINCT p.id)')
        );
        
        $select->order('p.update_date DESC');
        $select->joinLeft(array('pt' => 'page_tag'), 'pt.page_id = p.id', null);
        $select->join(array('c' => 'category'), 'c.id = p.category_id', null);
        $select->join(array('t' => 'tag'), 't.id = pt.tag_id', null);
        $select->setIntegrityCheck(false);

        $select->orwhere('p.title LIKE ?', "%{$keyword}%");
        $select->orwhere('p.contents LIKE ?', "%{$keyword}%");
        $select->orwhere('p.outline LIKE ?', "%{$keyword}%");
        $select->orwhere('p.title LIKE ?', "%{$keyword}%");

        return $this->fetchAll($select);
        
    }

}
