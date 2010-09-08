<?php
/**
 * 管理側のタグ管理用サービス
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
 * タグ管理サービス
 * 
 * @package    Admin
 * @subpackage Model
 * @author     saniker10, suzuki-mar
 */
class Admin_Model_Tag
{
	/**
	 * タグ情報タグを取得する	 
	 *
	 * @return array 	タグ情報の一覧
	 * @author  saniker10, suzuki-mar
	 */
	 public function getTags()
	 {
       $result[] = array('name' => 'タグ1', 	'id' => 1);
	   $result[] = array('name' => 'タグ2', 	'id' => 2);
	   $result[] = array('name' => '新規タグ', 	'id' => 3);
	   return $result;
    }

}
