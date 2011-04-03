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
class Common_Model_DbTable_Page extends Setuco_Db_Table_Abstract
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
     * ページの状態　公開
     */
    const STATUS_OPEN = 1;

    /**
     * ページの状態　下書き
     */
    const STATUS_DRAFT = 0;

    /**
     * 新着ページを取得する
     *
     * @param int $limit 何件のページを取得するのか
     * @return 新着ページのデータ
     * @author suzuki-mar
     */
    public function loadLastUpdatePages($limit)
    {
        $select = $this->select();

        //公開しているものしか取得しない
        $select->where('status = ?', self::STATUS_OPEN);

        //編集日時が最新順にソートする
        $select->order('update_date DESC');

        //指定した件数しか取得しない
        $select->limit($limit);

        $result = $this->fetchAll($select)->toArray();
        return $result;
    }

    /**
     * 最近作成(公開)したページを取得する
     *
     * @param  int     $limit          何件のページを取得するのか
     * @param  boolean $isJoinCategory カテゴリーテーブルを結合するならtrue。デフォルトはしない
     * @param  boolean $isJoinAccount  アカウントテーブルを結合するならtrue。 デフォルトはしない
     * @author charlesvineyard
     */
    public function loadLastCreatedPages($limit, $isJoinCategory = false, $isJoinAccount = false)
    {
        $select = $this->select();
        $select->from(array('p' => $this->_name));

        if ($isJoinCategory) {
            $select->joinLeft(array('c' => 'category'), 'c.id = p.category_id', array('category_name' => 'c.name'))
                    ->setIntegrityCheck(false);
        }

        if ($isJoinAccount) {
            $select->join(array('a' => 'account'), 'p.account_id = a.id', array('account_id' => 'a.id', 'a.nickname'))
                    ->setIntegrityCheck(false);
        }

        $select->where('status = ?', self::STATUS_OPEN)
                ->order('create_date desc')
                ->limit($limit);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * 未分類のカテゴリーのページ数をカウントする
     *
     * @param string[option] $status 公開しているものを取得する場合は、open 非公開のものはdraft
     * @return int 未分離のカテゴリーのページ数
     * @author suzuki-mar
     */
    public function countUncategorizedPage($status = null)
    {
        $select = $this->select();
        if ($status === 'draft') {
            $select->where('status = ?', self::STATUS_DRAFT);
        } elseif ($status === 'open') {
            $select->where('status = ?', self::STATUS_OPEN);
        }
        $select->where('category_id IS NULL');

        return $this->fetchAll($select)->count();
    }

    /**
     * ページの状態と作成日時の幅を指定して、期間中に作られたページを数えます。
     *
     * @param  int $status ページの状態（Setuco_Data_Constant_Page::STATUS_*）
     *                     指定しなければ全ての状態のものを数えます。
     * @param  Zend_Date|string $createDateStart  作成日時の最小値(この値自体を含む)
     * @param  Zend_Date|string $createDateEnd    作成日時の最大値(この値自体を含まない)
     * @return int ページ数
     * @author charlesvineyard
     */
    public function countPagesByStatusAndCreateDateSpan($status = null, $createDateStart = null, $createDateEnd = null)
    {
        $select = $this->select();
        if (!is_null($status)) {
            $select->where('status = ?', $status);
        }
        if (!is_null($createDateStart)) {
            $select->where('create_date >= ?', $createDateStart);
        }
        if (!is_null($createDateEnd)) {
            $select->where('create_date < ?', $createDateEnd);
        }
        return $this->fetchAll($select)->count();
    }

    /**
     * カテゴリを指定してページを取得する。pageNumberとlimitの両方が指定された場合だけ、ページネータ用のデータを取得する。
     *
     * @param  int    $categoryId 取得したいカテゴリのID
     * @param  int    $status     ページの状態。デフォルトは全て。
     * @param  int    $pageNumber ページネータの何ページ目を表示するか
     * @param  int    $limit      １ページに表示するページ数
     * @param  string $sortColumn 並べ替えるカラム名
     * @param  string $order      並べ替えの順序。 昇順ならASC。降順ならDESC。
     * @author akitsukada
     * @return array 取得したページデータ
     */
    public function loadPagesByCategoryId4Pager($categoryId, $status = null, $pageNumber = null, $limit = null, $sortColumn = 'update_date', $order = 'DESC')
    {
        $select = $this->select();

        $select->from(array('p' => $this->_name));

        //投稿者名取得のためJOIN

        $select->join(array('a' => 'account'), 'p.account_id = a.id', array('account_id' => 'a.id', 'a.nickname'));
        $select->setIntegrityCheck(false);

        // 指定された状態のページのみ取得する
        if (!is_null($status)) {
            $select->where('status = ?', $status);
        }

        //指定されたカテゴリのページのみ取得する
        if (is_null($categoryId)) {
            $select->where('category_id is null');
        } else {
            $select->where('category_id = ?', (int) $categoryId);
        }

        // ソート指定
        $select->order("{$sortColumn} {$order}");

        if (!is_null($pageNumber) && !is_null($limit)) {
            //ページネータの設定（何ページ目を表示するか、何件ずつ表示するか）
            $select->limitPage($pageNumber, $limit);
        }

        return $this->fetchAll($select)->toArray();
    }

    /**
     * タグIDを指定してページを取得する。pageNumberとlimitの両方が指定された場合だけ、ページネータ用のデータを取得する。
     *
     * @param int $tagId 取得したいタグのID
     * @param int $pageNumber ページネータで何ページ目を表示するか
     * @param int $limig １ページに表示するページ数
     * @return array 取得したページデータを格納した配列
     * @author akitsukada
     */
    public function loadPagesByTagId4Pager($tagId, $pageNumber = null, $limit = null)
    {
        $select = $this->select();

        $select->from(array(
            'p' => $this->_name
        ));


        //編集日時の降順にソートする
        $select->order('p.update_date DESC');

        $select->join(array('pt' => 'page_tag'), 'pt.page_id = p.id');

        $select->setIntegrityCheck(false);

        $select->where('p.status = ?', self::STATUS_OPEN);
        $select->where('pt.tag_id = ?', $tagId);

        if (!is_null($pageNumber) && !is_null($limit)) {
            //ページネータの設定（何ページ目を表示するか、何件ずつ表示するか）
            $select->limitPage($pageNumber, $limit);
        }

        return $this->fetchAll($select)->toArray();
    }

    /**
     * ページをキーワード&タグIDで検索し、ページネータ用のページデータを取得する。
     *
     * @param string $keyword 検索したいキーワード。
     * @param array $tagIds 検索したいタグのID。
     * @param int $pageNumber ページネータで何ページ目を表示するか。
     * @param int $limit ページネータで１ページに何件表示するか。
     * @return array 取得したページデータを格納した配列。
     * @author akitsukada charlesvineyard
     */
    public function loadPagesByKeyword4Pager($keyword, $tagIds, $pageNumber, $limit, $targetColumns = null, $refinements = null, $sortColumn = 'update_date', $order = 'DESC')
    {
        $select = $this->_createSelectByKeyword($keyword, $tagIds, $targetColumns, $refinements, $sortColumn, $order);

        $select->limitPage($pageNumber, $limit);
        return $this->fetchAll($select)->toArray();
    }

    /**
     * ページをキーワード&タグIDで検索し、該当するページの合計数を求める。
     *
     * @param string $keyword 検索したいキーワード。
     * @param array $tagIds 検索したいタグのID。
     * @param array $targetColumns 検索対象のカラムの配列
     * @return int 検索条件に合致したページの数。
     */
    public function countPagesByKeyword($keyword, $tagIds, $targetColumns, $refinements = null)
    {
        $select = $this->_createSelectByKeyword($keyword, $tagIds, $targetColumns, $refinements);
        return count($this->fetchAll($select));
    }

    /**
     * キーワード検索用のセレクトを作成します。
     *
     * @param string $keyword 検索したいキーワード。
     * @param array $tagIds 検索したいタグのID。
     * @param int $pageNumber ページネータで何ページ目を表示するか。
     * @param int $limit ページネータで１ページに何件表示するか。
     * @param boolean $isCounting 件数を取得するセレクトなら true。行を取得するなら false。
     * @param array $targetColumns 検索対象のカラム名の配列
     * @return Zend_Db_Table_Select
     * @author akitsukada charlesvineyard
     */
    private function _createSelectByKeyword($keyword, $tagIds, $targetColumns, $refinements = null, $sortColumn = 'update_date', $order = 'DESC')
    {
        $select = $this->select();
        $select->from(
                array('p' => $this->_name),
                array('*')
        );

        // ORDER BY
        $select->order("{$sortColumn} {$order}");
        // JOIN
        $select->joinLeft(array('pt' => 'page_tag'), 'pt.page_id = p.id', array());
        $select->joinLeft(array('c' => 'category'), 'c.id = p.category_id', array('category_name' => 'c.name'));
        $select->joinLeft(array('t' => 'tag'), 't.id = pt.tag_id', array(/* 'tag_name' => 't.name' */)); // タグ情報はここではつけない
        $select->join(array('a' => 'account'), 'p.account_id = a.id', array('account_id' => 'a.id', 'a.nickname'));
        $select->setIntegrityCheck(false);

        // 必要ならデフォルトの検索対象列を設定
        if ($targetColumns == null) {
            $targetColumns = array('title', 'contents', 'outline', 'tag');
        }

        // グルーピングの指定
        $select->group('p.id');

        // WHERE句の生成
        $orwhere = '';
        $keyword = $this->escapeLikeString($keyword);
        $bind = array();
        if (in_array('title', $targetColumns)) {
            $orwhere .= " (";
            $orwhere .= $this->getBsReplacedExpression('p.title') . " LIKE :keyword";
            $bind[':keyword'] = "%{$keyword}%";
            $orwhere .= ")";
        }

        if (in_array('contents', $targetColumns)) {
            if ($orwhere !== '') {
                $orwhere .= ' OR ';
            }
            $orwhere .= " (";
            $orwhere .= $this->getBsReplacedExpression('p.contents') . " LIKE :keyword";
            $bind[':keyword'] = "%{$keyword}%";
            if ($keyword !== '' && !is_null($keyword)) {
                $orwhere .= " AND (p.contents NOT REGEXP :exp1 OR p.contents REGEXP :exp2) ";
                $bind[':exp1'] = "<[^>]*{$keyword}[^<]*>";
                $bind[':exp2'] = ">[^<]*{$keyword}+";
            }
            $orwhere .= ")";
        }

        if (in_array('outline', $targetColumns)) {
            if ($orwhere !== '') {
                $orwhere .= ' OR ';
            }
            $orwhere .= " (";
            $orwhere .= $this->getBsReplacedExpression('p.outline') . " LIKE :keyword";
            $bind[':keyword'] = "%{$keyword}%";
            $orwhere .= ")";
        }

        if (in_array('tag', $targetColumns)) {
            if ($orwhere !== '') {
                $orwhere .= ' OR ';
            }
            $orwhere .= " (";
            if (is_array($tagIds) && !empty($tagIds)) {
                $orwhere .= ' t.id IN(:tagIds) ';
                $bind[':tagIds'] = implode(",", $tagIds);
            } else {
                // 該当するタグが無かったときは明示的にfalseとする
                $orwhere .= 't.id <> t.id';
            }
            $orwhere .= ")";
        }

        

        if ($orwhere !== '') {
            $select->where($orwhere);
        }

        // カテゴリー・制作者・公開状態が指定された場合にWhere句を編集
        if (is_array($refinements) && !empty($refinements)) {
            foreach ($refinements as $column => $value) {
                if ($column === 'category_id' && $value === null) {
                    $select->where("category_id is null");
                    continue;
                }
                $select->where("{$column} = :{$column}");
                $bind[":{$column}"] = $value;
            }
        }

        array_unique($bind);
        $select->bind($bind);
        return $select;
    }

    /**
     * 指定した並び順とオフセットでページ一覧を取得します。
     *
     * @param string  $sortColmn  並べ替えをするカラムのカラム名
     * @param string  $order 並び順 ASC か DESC
     * @param int $page 取得するページ番号
     * @param int $limit 1ページあたり何件のデータを取得するのか
     * @param boolean $isJoinAccount アカウントテーブルを結合するなら true。 デフォルトは false
     * @return array ページ情報の配列
     * @author charlesvineyard
     */
    public function loadPages4Pager($sortColumn, $order, $pageNumber, $limit, $isJoinAccount = false)
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        if ($isJoinAccount) {
            $select->setIntegrityCheck(false)
                    ->join(array('a' => 'account'),
                            'page.account_id = a.id',
                            array('account_id' => 'a.id', 'a.nickname'));
        }
        $select->limitPage($pageNumber, $limit)->order("{$sortColumn} {$order}");

        return $this->fetchAll($select)->toArray();
    }

}
