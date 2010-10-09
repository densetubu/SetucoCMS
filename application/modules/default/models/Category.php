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
class Default_Model_Category
{
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
	public function getCategoryList() 
	{
		//未分類以外のカテゴリーを取得する
		$categories = $this->_dao->findCategoryList(true);
		
		
		//取得できなかったか
		if ($categories === false) {
			$isNoData[] = true;
		}
		
		//未分類のカテゴリーを取得する
		$defaultcategories = $this->_dao->findDefault();
	   
		//取得できなかったか
        if ($defaultcategories === false) {
            $isNoData[] = true;
        } else {
        	$categories[] = $defaultcategories;
        }
		
        //未分類も登録するカテゴリーもなかったらfalseを返す
        if (isset($isNoData)) {
        	return false;
        }
		
	
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
		
		return $result;
	}
	
}



