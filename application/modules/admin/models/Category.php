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
	 * @return array 	カテゴリー情報の一覧
	 * @author  saniker10, suzuki-mar
     * @todo スタブのデータを取得している
	 */
	 public function getCategories()
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

}
