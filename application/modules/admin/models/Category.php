<?php

/**
 * 管理側のカテゴリー管理用サービス
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
        return $this->_categoryDao->loadCategories4Pager($order, $pageNumber, $limit);
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

        $categoryData = $this->_categoryDao->loadByPrimary($id);

        if ($categoryData === null) {
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
     *
     * @return int 何件該当したデータが存在したか
     * @author suzuki-mar
     */
    public function countAllCategories()
    {
        return $this->_categoryDao->countAll();
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
        $category = $this->_categoryDao->loadByPrimary($id);

        //取得できたら、trueにする
        $result = (boolean) $category;

        return $result;
    }

    /**
     * カテゴリーを新規作成する
     * コントローラーから、バリデートチェックした入力パラメーターをすべて取得する
     *
     * @param array $registData 新規登録するカテゴリーのデータ
     * @return int insertしたレコードのプライマリーキー
     * @throws insert文の実行に失敗したら例外が発生する
     * @author suzuki-mar
     * @todo バージョン2以降では、parent_idの設定をする
     *
     */
    public function registCategory($registData)
    {
        //バージョン1では、parent_idは固定値
        $registData['parent_id'] = Common_Model_DbTable_Category::PARENT_ROOT_ID;
        return $this->_categoryDao->insert($registData);
    }

    /**
     * カテゴリーを編集する
     * コントローラーから、バリデートチェックした入力パラメーターをすべてと、編集するidを取得する
     *
     * @param int   $id  アップデートするデータのID
     * @param array $categoryInfo 入力したデータ: バリデートチェックした入力データ
     * @return boolean 編集したか
     * @throws update文の実行に失敗したら例外が発生する
     * @author suzuki-mar
     */
    public function updateCategory($id, $updateData)
    {
        return $this->_categoryDao->updateByPrimary($updateData, $id);
    }

    /**
     * カテゴリーを削除する
     *
     * コントローラーから、削除するidを取得する
     *
     * @param  int   $deleteId  削除するデータのID
     * @return boolean 削除したか
     * @throws delete文の実行に失敗したら例外が発生する
     * @author suzuki-mar
     */
    public function deleteCategory($id)
    {
        return $this->_categoryDao->deleteByPrimary($id);
    }

}
