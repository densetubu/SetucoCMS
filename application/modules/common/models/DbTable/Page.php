<?php

/**
 * pageテーブルのDbTable(DAO)クラスです。
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
     * キーワード検索のプレースホルダーのプレフィックス
     */
    const PLACE_HOLDER_PREFIX_KEYWORD = 'keyword';

    /**
     * 開始タグチェックのプレースホルダーのプレフィックス
     */
    const PLACE_HOLDER_PREFIX_CHECK_START = 'startExp';

    /**
     * 終了タグチェックのプレースホルダーのプレフィックス
     */
    const PLACE_HOLDER_PREFIX_CHECK_END = 'endExp';

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
    public function loadLastCreatedPages($limit, $isJoinCategory = false,
            $isJoinAccount = false)
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
    public function countPagesByStatusAndCreateDateSpan($status = null,
            $createDateStart = null, $createDateEnd = null)
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
    public function loadPagesByCategoryId4Pager($categoryId, $status = null,
            $pageNumber = null, $limit = null, $sortColumn = 'update_date',
            $order = 'DESC')
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
            $select->where('category_id IS NULL');
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
    public function loadPagesByTagId4Pager($tagId, $pageNumber = null,
        $limit = null)
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
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return array 取得したページデータを格納した配列。
     * @author akitsukada charlesvineyard suzuki-mar
     */
    public function loadPagesByKeyword4Pager(Common_Model_Page_Param $paramIns)
    {
        
        $select = $this->_createSelectByKeyword($paramIns);

        $select->limitPage($paramIns->getPageNumber(), $paramIns->getLimit());

        return $this->fetchAll($select)->toArray();
    }

//    public function loadPagesByKeyword4Pager(Common_Model_Page_Param $paramIns)
//    {
//        $select = $this->_createSelectByKeyword($paramIns);
//        $select->limitPage($paramIns->getPageNumber(), $paramIns->getLimit());
//        return $this->fetchAll($select)->toArray();
//    }

    /**
     * ページをキーワード&タグIDで検索し、該当するページの合計数を求める。
     *
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return int 検索条件に合致したページの数。
     * @author akitsukada suzuki-mar
     */
    public function countPagesByKeyword(Common_Model_Page_Param $paramIns)
    {
        $select = $this->_createSelectByKeyword($paramIns);
        return count($this->fetchAll($select));
    }

    /**
     * キーワード検索用のセレクトを作成します。
     *
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return Zend_Db_Table_Select
     * @author akitsukada charlesvineyard suzuki-mar
     */
    private function _createSelectByKeyword(Common_Model_Page_Param $paramIns)
    {
        $select = $this->select();

        $select->from(
                array('p' => $this->_name),
                array('*')
        );

        // ORDER BY
        $select->order("{$paramIns->getSortColumn()} {$paramIns->getOrder()}");
        $select = $this->_joinTable4KeywordSearch($select, $paramIns);
        // グルーピングの指定
        $select->group('p.id');


        $bind = array();

        //キーワード検索用のWHERE
        if ($paramIns->isSearchKeyword() || $paramIns->isTargetTag()) {
            $bind = array_merge($bind, $this->_createPageSearchBinds($paramIns));
            $select = $this->_addKeywordWhere4SearchKeyword($select, $paramIns);
        }

        // カテゴリー・制作者・公開状態が指定された場合にWhere句を編集
        if ($paramIns->isSearchRefinements()) {
            $select = $this->_addRefinementsWhere4SearchKeyword($select, $paramIns);
            $bind = array_merge($bind, $this->_createRefinementsBind4SearchKeyword($paramIns));
        }

        array_unique($bind);
        $select->bind($bind);

        return $select;
    }

    /**
     * 詳細検索用のwhere句をselectインスタンスに追加する
     *
     * @param Zend_Db_Table_Select 詳細検索用のWHERE句を追加するSelectインスタンス
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return Zend_Db_Table_Selec 詳細検索用のWHEREを追加した物
     */
    private function _addRefinementsWhere4SearchKeyword(Zend_Db_Table_Select $select, Common_Model_Page_Param $paramIns)
    {
       foreach ($paramIns->getRefinements() as $column => $value) {
            if ($column === 'category_id' && $value === null) {
                $select->where("category_id IS NULL");
                continue;
            }
            $select->where("{$column} = :{$column}");
       }

       return $select;

    }

    /**
     * 詳細検索用のbindを作成する
     *
     * @param Zend_Db_Table_Select 詳細検索用のWHERE句を追加するSelectインスタンス
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return Zend_Db_Table_Selec 詳細検索用のWHEREを追加した物
     * @author suzuki-mar
     */
     private function _createRefinementsBind4SearchKeyword(Common_Model_Page_Param $paramIns)
     {
        $bind = array();

        foreach ($paramIns->getRefinements() as $column => $value) {
            if ($column === 'category_id' && $value === null) {
                continue;
            }
            $bind[":{$column}"] = $value;
        }

        return $bind;
     }

    /**
     * キーワード検索のSelectインスタンスをがテーブル結合する
     * @param Zend_Db_Table_Select 初期化をするSelectインスタンス
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return Zend_Db_Table_Select 初期化をしたSelectインスタンス
     * @author suzuki_mar
     */
    private function _joinTable4KeywordSearch(Zend_Db_Table_Select $select, Common_Model_Page_Param $paramIns)
    {
        $select->joinLeft(array('pt' => 'page_tag'), 'pt.page_id = p.id', array());
        $select->joinLeft(array('c' => 'category'), 'c.id = p.category_id', array('category_name' => 'c.name'));
        $select->joinLeft(array('t' => 'tag'), 't.id = pt.tag_id', array(/* 'tag_name' => 't.name' */)); // タグ情報はここではつけない
        $select->join(array('a' => 'account'), 'p.account_id = a.id', array('account_id' => 'a.id', 'a.nickname'));
        $select->setIntegrityCheck(false);
        
        return $select;
    }


    /**
     * 検索キーワード用のWhere句にセットするバインド変数をセットする
     *
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return array 検索キーワードにセットするバインド変数
     * @author suzuki-mar
     */
    private function _createPageSearchBinds(Common_Model_Page_Param $paramIns)
    {
        $result = array();

        if ($paramIns->isSearchKeyword()) {
            $result = Setuco_Sql_Generator::createMultiLikeTargets($paramIns->getKeyword(), self::PLACE_HOLDER_PREFIX_KEYWORD);
        }

        if ($paramIns->isTargetTag()) {
            $result[':tagIds'] = implode(",", $paramIns->getTagIds());
        }

        return $result;


//
//        //contentsのみタグが含まれていないかのチェックをする
//        if (in_array('contents', $paramIns->getTargetColumns()) && !$paramIns->isEmpty('keyword')) {
//            $result = array_merge($result,
//                            Setuco_Sql_Generator::createMulitiBindParams($paramIns->getKeyword(), self::PLACE_HOLDER_PREFIX_CHECK_START, '<[^>]*', '[^<]*>'),
//                            Setuco_Sql_Generator::createMulitiBindParams($paramIns->getKeyword(), self::PLACE_HOLDER_PREFIX_CHECK_END, '>[^<]*', '+'));
//        }
//
       
    }

    /**
     * ページ検索用のWHERE句を生成する
     *
     * @param Zend_Db_Table_Select $select 初期化をするSelectインスタンス
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return string ページ検索用のWHERE句
     * @author suzuki-mar
     */
    private function _addKeywordWhere4SearchKeyword(Zend_Db_Table_Select $select, Common_Model_Page_Param $paramIns)
    {
        $where = '';
        $paramIns->setEscapeKeyword(Setuco_Sql_Generator::escapeLikeString($paramIns->getKeyword()));

        foreach ($paramIns->getKeywordSearchColumns() as $columnName) {
            if ($paramIns->isInTargetColumn($columnName)) {

                if ($where !== '') {
                    $where .= ' OR ';
                }

                $searchColumnName = Setuco_Sql_Generator::createBsReplacedExpression($columnName);
                $where .= Setuco_Sql_Generator::createMultiLike4Keyword($paramIns->getEscapeKeyword(), $searchColumnName, self::PLACE_HOLDER_PREFIX_KEYWORD, $paramIns->getSearchOperator());
            }
        }

        if ($paramIns->isTargetTag()) {
            if ($where !== '') {
                $where .= ' OR ';
            }

            $where .= " (t.id IN(:tagIds))";
        }

        $select->where($where);

        return $select;
    }

//    private function _createPageSearchWhere(Common_Model_Page_Param $paramIns)
//    {
//
//        if (in_array('contents', $paramIns->getTargetColumns())) {
//            if ($result !== '') {
//                $result .= ' OR ';
//            }
//
//            //タグを検索しないようにするための条件も合わせて設定する
//            $result .= $this->_createContentsMulitiLike($paramIns);
//        }
//
//



    /**
     * 引数で渡した文字列を分解して複数のLIKE用のSQLを生成する
     * タグを検索しないようするために、Generatorのメソッドを使用できない
     *
     * @param Common_Model_Page_Param $paramIns 検索パラメーターオブジェクト
     * @return string コンテンツを検索するためのWHERE句
     * @author suzuki-mar
     */
    private function _createContentsMulitiLike(Common_Model_Page_Param $paramIns)
    {

        $targetLists = Setuco_Util_String::convertArrayByDelimiter($paramIns->getEscapeKeyword());
        $columnName = Setuco_Sql_Generator::createBsReplacedExpression('p.contents');
        $result = "(";

        for ($i = 0; $i < count($targetLists); $i++) {
            $result .= " (";
            $result .= $columnName . " LIKE :" . self::PLACE_HOLDER_PREFIX_KEYWORD . $i;
            if ($targetLists[$i] !== '' && !is_null($targetLists[$i])) {
                $result .= " AND (p.contents NOT REGEXP :" . self::PLACE_HOLDER_PREFIX_CHECK_START . "{$i} OR p.contents REGEXP :" . self::PLACE_HOLDER_PREFIX_CHECK_END . "{$i}) ";
            }
            $result .= ") {$paramIns->getSearchOperator()}";
        }

        $result = substr($result, 0, -3);
        $result .= ")";

        return $result;
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
    public function loadPages4Pager($sortColumn, $order, $pageNumber, $limit,
            $isJoinAccount = false)
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
