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
class Default_Model_Category
{

    /**
     * モデルが使用するDAO(DbTable)クラスを設定する
     *
     * @var Zend_Db_Table
     */
    protected $_dao = null;

    /**
     * 初期設定をする
     *
     * @author suzuki_mar
     */
    public function __construct()
    {
        $this->_dao = new Common_Model_DbTable_Category();
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
        $categories = $this->_dao->findCategoryLists(true);


        //取得できた場合のみ整形する
        if ($categories !== false) {

            foreach ($categories as $value) {
                //使用しているかどうかを判定する
                if (is_null($value['title'])) {
                    $value['is_used'] = false;
                } else {
                    $value['is_used'] = true;
                }

                //必要のないものは削除する
                unset($value['title'], $value['parent_id']);
                $result[] = $value;
            }
        } else {
            $isNoData = true;
        }

        if (isset($result)) {
            //未分類のカテゴリーを追加する
            $result = $this->_dao->addDefaultCategory($result);
        } else { //カテゴリーがなかったら未分類カテゴリーのみ追加する
            $result = $this->_dao->addDefaultCategory();
        }

        return $result;
    }

    /**
     * カテゴリーIDを指定してカテゴリー情報を取得する
     *
     * @param int $id 取得したいページのカテゴリーID
     * @return array|boolean 該当のデータが存在すれば配列データ、存在しなければfalseを返す
     * @author akitsukada
     */
    public function getCategoryById($id)
    {
        return $this->_dao->findById($id);
    }
}

