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
 * @author     suzuki-mar
 */

/**
 * ページ情報管理クラス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar
 */
class Default_Model_Page
{

    /**
     * モデルが使用するDAO(DbTable)クラスを設定する
     * 
     * @var Zend_Db_Table
     */
    protected $_pageDao = null;

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
     * カテゴリを指定して記事を取得する
     *
     * @param int $catId 取得したいカテゴリのID
     * @author akitsukada
     * @return
     */
    public function getPagesByCategoryId($catId, $currentPage, $limit = self::LIMIT_GET_PAGE_BY_CATEGORY)
    {
        return $this->_pageDao->findPagesByCategoryId($catId, $currentPage, $limit);
    }

    /**
     * 指定したカテゴリに属するページの数を取得する
     */
    public function countPagesByCategoryId($catId)
    {
        return count($this->_pageDao->findPagesByCategoryId($catId));
    }

    /**
     * タグを指定して記事を取得する
     *
     * @param int $tagId 取得したいタグID
     * @author akitsukada
     * @return 
     */
    public function getPagesByTagId($tagId, $currentPage, $limit = self::LIMIT_GET_NEW_PAGE)
    {
        return $this->_pageDao->findPagesByTagId($tagId, $currentPage, $limit)->toArray();
    }

    /**
     * 指定したカテゴリに属するページの数を取得する
     */
    public function countPagesByTagId($tagId)
    {
        return count($this->_pageDao->findPagesByTagId($tagId));
    }

    public function find($id)
    {
        return $this->_pageDao->find($id)->toArray();
    }

    public function search($keyword, $currentPage = 1, $limit = self::LIMIT_GET_NEW_PAGE)
    {
        return $this->_pageDao->searchPage($keyword, $currentPage, $limit)->toArray();
        
    }
    public function countPagesByKeyword($keyword)
    {
        $queryResult = $this->_pageDao->countPagesByKeyword($keyword)->toArray();
        $count = $queryResult[0]['page_count'];
        return (int)$count;
    }
}

