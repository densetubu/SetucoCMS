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
    protected $_dao = null;

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
        $this->_dao = new Common_Model_DbTable_Page();
    }

    /**
     * 最新の記事を取得する
     *
     * @param int[option] 何件のデータを取得するのか　標準は10件
     * @author suzuki-mar
     */
    public function getNewPages($limitGetNewPage = self::LIMIT_GET_NEW_PAGE)
    {
        $result = $this->_dao->findNewPages($limitGetNewPage);

        return $result;
    }

    /**
     * カテゴリを指定して記事を取得する
     *
     * @param int $catId 取得したいカテゴリのID
     * @author akitsukada
     * @return
     */
    public function getPagesByCategory($catId, $currentPage, $limit = self::LIMIT_GET_PAGE_BY_CATEGORY)
    {
        return $this->_dao->findPagesByCategoryId($catId, $currentPage, $limit);
    }

    /**
     * 指定したカテゴリに属するページの数を取得する
     */
    public function countPagesByCategory($catId)
    {
        return count($this->_dao->findPagesByCategoryId($catId));
    }


}

