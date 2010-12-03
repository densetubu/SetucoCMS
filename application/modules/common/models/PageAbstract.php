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
        return $this->_pageDao->find($id)->current()->toArray();
    }


    /**
     * 記事のキーワード検索を行う。検索対象はタイトル、本文、概要、タグ。（ページネータ対応）
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
        
        return $this->_pageDao->searchPages(
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
     * 記事のキーワード検索結果の合計数を求める。
     *
     * @param string $keyword 検索キーワード
     * @param array $targetColumns 検索対象の配列 (title|contents|outline|tag)
     * @param array $refinements 絞り込み条件 カテゴリー、アカウント、状態を指定
     * @return int 該当する記事の合計数
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
        return $this->_tagDao->findTagIdsByTagName($keyword);
    }
}

