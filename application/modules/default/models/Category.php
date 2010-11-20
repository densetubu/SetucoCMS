<?php
/**
 * 閲覧側のカテゴリー管理用サービス
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
 * カテゴリー管理クラス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar
 */
class Default_Model_Category extends Common_Model_CategoryAbstract
{
    /**
     * 初期設定をする
     *
     * @author suzuki_mar
     */
    public function __construct()
    {
        $this->_categoryDao = new Common_Model_DbTable_Category();
    }

    /**
     * カテゴリー情報を取得する
     *
     * @return array カテゴリー情報 取得できなかったらfalse
     * @author suzuki-mar
     */
    public function getCategoryLists()
    {
        //未分類以外のカテゴリーを取得する
        return $this->_categoryDao->findCategoryLists(true);
    }

    /**
     * カテゴリーIDを指定してカテゴリー情報を取得する
     * （DbTableクラスのfindメソッドへの委譲）
     *
     * @param int $id 取得したいページのカテゴリーID
     * @return array|boolean 該当のデータが存在すれば配列データ、存在しなければfalseを返す
     * @author akitsukada
     */
    public function findCategory($id)
    {
        $result = $this->_categoryDao->find($id)->current();
        if (is_null($result)) {
            return false;
        }
        return $result->toArray();
    }
    
}

