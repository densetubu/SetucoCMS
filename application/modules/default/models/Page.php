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
class Default_Model_Page extends Common_Model_PageAbstract
{
    /**
     * 新着記事表示用に標準で何件取得するか
     * @deprecated コントローラへ移動
     */
    const LIMIT_GET_NEW_PAGE = 10;

    /**
     * カテゴリ別検索で標準で何件取得するか
     * @deprecated コントローラへ移動
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
     * @todo limitのデフォルト修正
     */
    public function findLastUpdatedPages($limit = self::LIMIT_GET_NEW_PAGE)
    {
        //更新順の記事を取得する
        $result = $this->_pageDao->findLastUpdatePages($limit);
        
        //からならfalseを返す
        if (empty($result)) {
            $result = false;
        }
        
        return $result;
    }

    /**
     * カテゴリを指定して記事を取得する（ページネータ対応）
     *
     * @param int $categoryId 取得したいカテゴリのID
     * @author akitsukada
     * @return array 該当するカテゴリの記事データを格納した配列
     * @todo limitのデフォルト修正
     */
    public function findPagesByCategoryId($categoryId, $currentPage, $limit = self::LIMIT_GET_PAGE_BY_CATEGORY)
    {
        return $this->_pageDao->findPagesByCategoryId($categoryId, $currentPage, $limit);
    }

    /**
     * 指定したカテゴリに属する記事の数を取得する
     *
     * @param int $categoryId 記事数を取得したいカテゴリのID
     * @return int 該当する記事の数
     * @author akitsukada
     */
    public function countPagesByCategoryId($categoryId)
    {
        return count($this->_pageDao->findPagesByCategoryId($categoryId));
    }

    /**
     * タグを指定して記事を取得する
     *
     * @param int $tagId 取得したいタグID
     * @return array 該当するタグがつけられた記事のデータを格納した配列
     * @author akitsukada
     * @todo limitのデフォルト修正
     */
    public function findPagesByTagId($tagId, $currentPage, $limit = self::LIMIT_GET_NEW_PAGE)
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
}

