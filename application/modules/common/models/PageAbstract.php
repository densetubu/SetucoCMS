<?php
/**
 * ページ情報管理用サービス
 *
 * LICENSE: ライセンスに関する情報
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
        return $this->_pageDao->findById($id);
    }


    /**
     * ページのキーワード検索を行う。検索対象はタイトル、本文、概要、タグ。（ページネータ対応）
     *
     * @param string $keyword 検索したいテキスト。
     * @param int $currentPage ページネータの何ページ目を表示するか。
     * @param int $limit ページネータで１ページに何件表示するか。
     * @return array 検索結果を格納した配列
     * @author akitsukada
     * @todo 取得するカラムを動的にしたい。今は全取得。
     * @todo 引数まとめてクラス化する?
     */
    public function searchPages($keyword, $currentPage = 1, $limit = 10,
            $targetColumns = null, $refinements = null, $sortColumn = 'update_date', $order = 'DESC')
    {
        if ($targetColumns == null) {
            $targetColumns = $this->_searchTargetColumns;
        }

        return $this->_pageDao->loadPagesByKeyword(
            $keyword,
            $this->_searchTagIdsByKeyword($keyword),
            $currentPage,
            $limit,
            $targetColumns,
            $refinements,
            $sortColumn,
            $order
        );
    }

    /**
     * ページのキーワード検索結果の合計数を求める。
     *
     * @param string $keyword 検索キーワード
     * @param array $targetColumns 検索対象の配列 (title|contents|outline|tag)
     * @param array $refinements 絞り込み条件 カテゴリー、アカウント、状態を指定
     * @return int 該当するページの合計数
     * @author akitsukada
     */
    public function countPagesByKeyword($keyword, $targetColumns = null, $refinements = null)
    {
        if ($targetColumns == null) {
            $targetColumns = $this->_searchTargetColumns;
        }
        $tagIds = array();
        if (in_array('tag', $targetColumns)) {
            $tagIds = $this->_searchTagIdsByKeyword($keyword);
        }
        return (int)($this->_pageDao->countPagesByKeyword($keyword, $tagIds, $targetColumns, $refinements));
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
        return $this->_tagDao->loadTagIdsByTagName($keyword);
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
        return $this->_pageDao->loadPagesByCategoryId($categoryId, $status, $currentPage, $limit, $sortColumn, $order);
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
        return count($this->_pageDao->loadPagesByCategoryId($categoryId, $status));
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
        return $this->_pageDao->count();
    }
}

