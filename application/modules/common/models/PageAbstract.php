<?php
/**
 * 閲覧側のページ情報管理用サービス
 *
 * LICENSE: ライセンスに関する情報
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
abstract class Common_Model_PageAbstract
{
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
     * @todo limitのデフォルト修正
     */
    public function searchPages($keyword, $currentPage = 1, $limit = 10)
    {
        return $this->_pageDao->searchPages($keyword,
                $this->_searchTagIdsByTagName($keyword),
                $currentPage, $limit)
                ->toArray();
    }

    /**
     * 記事のキーワード検索結果の合計数を求める。
     *
     * @param string $keyword 
     * @return int 該当する記事の合計数
     * @author akitsukada
     */
    public function countPagesByKeyword($keyword)
    {
        return (int)($this->_pageDao->countPagesByKeyword(
                $keyword, $this->_searchTagIdsByTagName($keyword)));
    }

    /**
     * タグ名をキーワード検索し、該当するタグのIDを返す
     *
     * @param string $keyword 検索したいキーワード
     * @return array|null 該当するタグのIDを格納した配列
     */
    protected function _searchTagIdsByTagName($keyword)
    {
        return $this->_tagDao->findTagIdsByTagName($keyword);
    }
}

