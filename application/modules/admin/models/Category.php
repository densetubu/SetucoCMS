<?php

/**
 * 管理側のカテゴリー管理用サービス
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     saniker10, suzuki-mar
 */

/**
 * カテゴリー管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     saniker10, suzuki-mar
 */
class Admin_Model_Category extends Common_Model_CategoryAbstract
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
     * カテゴリーを取得する
     *
     * @param String $order カテゴリーを昇順か降順でソートするのか 文字列
     * @param int $pageNumber 取得するページ番号
     * @param int $limit 1ページあたり何件のデータを取得するのか
     * @return array カテゴリー情報の一覧
     * @author saniker10, suzuki-mar
     */
    public function findCategories($order, $pageNumber, $limit)
    {
        //pageを数値にキャストする
        $pageNumber = (int) $pageNumber;

        //ソートする方法をパラメータによって変更する desc意外は昇順(asc)
        if ($order === 'desc') {
            $order = "DESC";
        } else {
            $order = "ASC";
        }

        //指定したソートをしたデータを取得する
        $searchResult = $this->_categoryDao->loadSortCategories($order, $pageNumber, $limit);

        //配列の方が操作しやすいので配列を戻り値にする
        $result = $searchResult->toArray();

        return $result;
    }

    /**
     * カテゴリー名をIDから取得する
     *
     * @param int $id 取得するカテゴリーのID
     * @return Sring カテゴリー名 取得できなかったらfalse
     * @author suzuki-mar
     */
    public function findNameById($id)
    {
        $id = (int)$id;

        $categoryData = $this->_categoryDao->findById($id);

        if ($categoryData === false) {
            return false;
        }

        $result = $categoryData['name'];
        return $result;

    }

    /**
     * カテゴリーIDと名前のセットを取得する。
     *
     * @return array キー:カテゴリーID、値:カテゴリー名の配列
     * @author charlesvineyard
     */
    public function findAllCategoryIdAndNameSet()
    {
        $result = $this->_categoryDao->loadAllCategoriesSpecifiedColumns(array('id', 'name'), 'name');
        $idNameSet = array();
        foreach ($result as $row) {
            $idNameSet[$row['id']] = $row['name'];
        }
        return $idNameSet;
    }

    /**
     * 検索条件で、リミットしなかった場合に該当結果が何件あったのかを取得する
     * no-parentの文は除外する
     *
     * @return int 何件該当したデータが存在したか
     * @author suzuki-mar
     */
    public function countCategories()
    {
        $result = $this->_categoryDao->count();
        $result--;

        return $result;
    }

    /**
     * 指定したidのデータが存在するか
     *
     * @param  numeric 存在するかを調べるid
     * @return boolean 指定したidが存在するか
     * @author suzuki-mar
     */
    public function isExistsId($id)
    {

        //idのカテゴリーが存在するかを調べる
        $category = $this->_categoryDao->findById($id);

        //取得できたら、trueにする
        $result = (boolean) $category;

        return $result;
    }

    /**
     * カテゴリーを新規作成する
     * コントローラーから、バリデートチェックした入力パラメーターをすべて取得する
     *
     * @param array $registData 新規登録するカテゴリーのデータ
     * @return boolean 登録できたか　SQLが正常に実行できたら、成功とする
     * @author suzuki-mar
     */
    public function registCategory($registData)
    {
        //バージョン1では、nullにする
        $registData['parent_id'] = Common_Model_DbTable_Category::PARENT_ROOT_ID;

        //データをinsertする
        $this->_categoryDao->insert($registData);

        return true;
    }

    /**
     * カテゴリーを編集する
     * コントローラーから、バリデートチェックした入力パラメーターをすべてと、編集するidを取得する
     *
     * @param int   $id  アップデートするデータのID
     * @param array $categoryInfo 入力したデータ: バリデートチェックした入力データ
     * @return boolean 編集できたか SQLが正常に実行できたら、成功とする
     * @author suzuki-mar
     */
    public function updateCategory($id, $updateData)
    {
        //データをupdateする
        $primary = $this->_categoryDao->getPrimary();

        //数値にキャストする
        $id = (int)$id;
        //アップデートする条件のwhere句を生成する
        $where = $this->_categoryDao->getAdapter()->quoteInto("{$primary} = ?", $id);

        $this->_categoryDao->update($updateData, $where);

        return true;
    }

    /**
     * カテゴリーを削除する
     *
     * コントローラーから、削除するidを取得する
     *
     * @param  int   $deleteId  削除するデータのID
     * @return boolean 削除できたか SQLが正常に実行できたら成功とする
     * @author suzuki-mar
     */
    public function deleteCategory($id)
    {

        $primary = $this->_categoryDao->getPrimary();

        //文字列の可能性があるので、数値にキャストする
        $id = (int)$id;
        //アップデートする条件のwhere句を生成する
        $where = $this->_categoryDao->getAdapter()->quoteInto("{$primary} = ?", $id);
        $this->_categoryDao->delete($where);

        return true;
    }

}
