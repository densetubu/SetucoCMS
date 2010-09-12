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
class Admin_Model_Category
{
	/**
	 * タグ情報タグを取得する	 
	 * 
     * @param String $keyword 検索するカテゴリ名
     * @param int $page 現在のページ番号
     * @param int $limit 1ページあたり何件のデータを取得するのか
	 * @return array 	カテゴリー情報の一覧
	 * @author  saniker10, suzuki-mar
     * @todo スタブのデータを取得している
	 */
	 public function searchCategories($keyword, $page, $limit)
	 {
       $result[] = array('name' => 'about', 	        'id' => 1, 'is_check' => false);
	   $result[] = array('name' => 'contents', 	        'id' => 2, 'is_check' => true);
	   $result[] = array('name' => 'link', 	            'id' => 3, 'is_check' => false);
       $result[] = array('name' => 'report',            'id' => 4, 'is_check' => true);
       $result[] = array('name' => 'test',              'id' => 5, 'is_check' => true);
       $result[] = array('name' => '新規カテゴリー',    'id' => 6, 'is_check' => false);

       //デフォルトのカテゴリー　削除できない
       $result[] = array('name' => '未分類',            'id' => false, 'is_check' => false); 

	   return $result;
    }

    /**
     * 検索条件で、リミットしなかった場合に該当結果が何件あったのかを取得する
     * 
     * @param String $keyword 検索するキーワード
     * @return int 何件該当したデータが存在したか
     * @author suzuki-mar
     * @todo スタブのデータを取得している
     */
     public function countCategoriesByKeyword() 
     {
         $result = 50;
         return $result;
     }

     /**
      * 指定したidのデータが存在するか
      * 
      * @param  numeric 存在するかを調べるid
      * @return boolean 指定したidが存在するか
      * @author suzuki-mar
      * @todo   スタブのデータを取得している
      */
     public function isExistsId($id)
     {
         //同じデータを使用するため 実際は、DAOにSQLを発行させる
         $categories = $this->getCategories();

         //id一覧の配列を生成する
         foreach ($categories as $value) {
             $ids[] = $value['id'];
         }

         //指定したidが存在するか
         $result = in_array($id, $ids);

         return $result;
     }

     /**
      * カテゴリーを新規作成する
      * コントローラーから、バリデートチェックした入力パラメーターをすべて取得する
      *
      * @param array $inputData 入力したデータ: バリデートチェックした入力データ
      * @return boolean 登録できたか
      * @author suzuki-mar
      * @todo 現在は、スタブ(モック)メソッド
      */
     public function registCategory($inputData) 
     {
         //スタブの間は常にtrueを返す
         return true;
     }

     /**
      * カテゴリーを編集する

      * コントローラーから、バリデートチェックした入力パラメーターをすべてと、編集するidを取得する
      *
      * @param array $inputData 入力したデータ: バリデートチェックした入力データ
      * @param int   $updateId  アップデートするデータのID
      * @return boolean 編集できたか
      * @author suzuki-mar
      * @todo 現在は、スタブ(モック)メソッド
      */
     public function updateCategory($inputData, $updateId) 
     {

         //スタブの間は常にtrueを返す
         return true;
     }

     /**
      * カテゴリーを削除する
      * 
      * コントローラーから、削除するidを取得する
      *
      * @param  int   $deleteId  削除するデータのID
      * @return boolean 削除できたか
      * @author suzuki-mar
      * @todo 現在は、スタブ(モック)メソッド
      */
     public function deleteCategory($deleteId) 
     {

         //スタブの間は常にtrueを返す
         return true;
     }

}



