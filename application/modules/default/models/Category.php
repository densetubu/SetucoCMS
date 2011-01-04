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
    public function findCategoryLists()
    {
        //未分類以外のカテゴリーを取得する
        $categories = $this->_categoryDao->findAllCategories();

        if ($categories === false) {
            return false;
        }


        //使用されているカテゴリーを取得する
        $useCategories = $this->_categoryDao->findUsedCategories();


        if ($useCategories !== false) {
            //使用されているカテゴリーのIDの配列を取得する
            foreach ($useCategories as $value) {
                $useIds[] = $value['id'];
            }

            foreach ($categories as &$value) {
                $value['is_used'] = in_array($value['id'], $useIds);
            }
            unset($value);

            //ひとつも使用されていない場合は、すべて失敗にする
        } else {
            foreach ($categories as &$value) {
                $value['is_used'] = false;
            }
            unset($value);
        }

        return $categories;
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

