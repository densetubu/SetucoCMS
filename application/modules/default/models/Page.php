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
class Default_Model_Page
{

    /**
     * モデルが使用するPageテーブルのDAO(DbTable)クラスを設定する
     * 
     * @var Common_Model_DbTable_Page
     */
    protected $_pageDao = null;

    /**
     * モデルが使用するPageテーブルのDAO(DbTable)クラスを設定する
     *
     * @var Common_Model_DbTable_Tag
     */
    protected $_tagDao = null;

    /**
     * 新着記事表示用に標準で何件取得するか
     */
    const LIMIT_GET_NEW_PAGE = 10;

    /**
     * カテゴリ別検索で標準で何件取得するか
     */
    const LIMIT_GET_PAGE_BY_CATEGORY = 5;

    /**
     * 初期設定をする
     *
     * @author suzuki_mar
     */
    public function __construct()
    {
        $this->_pageDao = new Common_Model_DbTable_Page();
        $this->_tagDao = new Common_Model_DbTable_Tag();
    }

    /**
     * 最新の編集した記事を取得する
     *
     * @param int[option] 何件のデータを取得するのか　標準は10件　取得できない場合はfalseを返す
     * @author suzuki-mar
     */
    public function getLastUpdatePages($limitGetNewPage = self::LIMIT_GET_NEW_PAGE)
    {
    	//更新順の記事を取得する
        $result = $this->_pageDao->findLastUpdatePages($limitGetNewPage);
        
        //からならfalseを返す
        if (empty($result)) {
        	$result = false;
        }
        
        return $result;
    }

    /**
     * カテゴリを指定して記事を取得する（ページネータ対応）
     *
     * @param int $catId 取得したいカテゴリのID
     * @author akitsukada
     * @return array 該当するカテゴリの記事データを格納した配列
     */
    public function getPagesByCategoryId($catId, $currentPage, $limit = self::LIMIT_GET_PAGE_BY_CATEGORY)
    {
        return $this->_pageDao->findPagesByCategoryId($catId, $currentPage, $limit);
    }

    /**
     * 指定したカテゴリに属する記事の数を取得する
     *
     * @param int $catId 記事数を取得したいカテゴリのID
     * @return int 該当する記事の数
     * @author akitsukada
     */
    public function countPagesByCategoryId($catId)
    {
        return count($this->_pageDao->findPagesByCategoryId($catId));
    }

    /**
     * タグを指定して記事を取得する
     *
     * @param int $tagId 取得したいタグID
     * @return array 該当するタグがつけられた記事のデータを格納した配列
     * @author akitsukada
     */
    public function getPagesByTagId($tagId, $currentPage, $limit = self::LIMIT_GET_NEW_PAGE)
    {
        return $this->_pageDao->findPagesByTagId($tagId, $currentPage, $limit)->toArray();
    }

    /**
     * 指定したタグIDのタグがつけられたページの数を取得する
     *
     * @param int $tagId カウントしたいタグのID
     * @return int 該当するページ数
     * @author akitsukada
     */
    public function countPagesByTagId($tagId)
    {
        return count($this->_pageDao->findPagesByTagId($tagId));
    }

    /**
     * IDを指定して記事を一件取得する
     *
     * @param int $id 取得したい記事のID
     * @return array 該当するIDの記事データを格納した配列
     * @author akitsukada
     */
    public function findPage($id)
    {
        return $this->_pageDao->find($id)->toArray();
    }

    /**
     * 記事のキーワード検索を行う。検索対象はタイトル、本文、概要、タグ。（ページネータ対応）
     *
     * @param string $keyword 検索したいテキスト。
     * @param int $currentPage ページネータの何ページ目を表示するか。
     * @param int $limit ページネータで１ページに何件表示するか。
     * @return array 検索結果を格納した配列
     * @author akitsukada
     */
    public function searchPages($keyword, $currentPage = 1, $limit = self::LIMIT_GET_NEW_PAGE)
    {
        $tagIds = $this->_tagDao->findTagIdsByTagName($keyword);
        return $this->_pageDao->searchPages($keyword, $this->_findTagIdsByTagName($keyword), $currentPage, $limit)->toArray();
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
        return (int)($this->_pageDao->countPagesByKeyword($keyword, $this->_findTagIdsByTagName($keyword)));
    }

    /**
     * タグ名をキーワード検索し、該当するタグのIDを返す
     *
     * @param string $keyword 検索したいキーワード
     * @return array|null 該当するタグのIDを格納した配列
     */
    private function _findTagIdsByTagName($keyword)
    {
        $tagIds = $this->_tagDao->findTagIdsByTagName($keyword);
        return $tagIds;
    }
}

