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
	public function getCategoryList()
	{
		//未分類以外のカテゴリーを取得する
		$categories = $this->_dao->findCategoryList(true);


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
			$result = $this->addDefaultCategory($result);
		} else { //カテゴリーがなかったら未分類カテゴリーのみ追加する
			$result = $this->addDefaultCategory();
		}

		return $result;
	}
	
	/**
	 * 未分類のカテゴリーを追加したカテゴリーを取得する
	 * 
	 * @param array[option] $subjects 元となる配列
	 * @return array 未分類のカテゴリーを追加したもの 未分類のカテゴリーはis_defaultの要素がある
	 */
	public function addDefaultCategory($subjectes = array()) 
	{
		$default[0] = array('id' => -2, 'name' => '未分類', 'is_default' => true);
		
		//カテゴリーが新規作成されていない場合もリンクする
		$isLink = empty($subjectes);
		foreach ($subjectes as $value) {
            //一つでも使用していない場合は、リンクする 	
			if ($value['is_used'] !== true) {
				$isLink = true;
				break;
			}
		}
		
		
		$default[0]['is_used'] = $isLink;
		
        
		//未分類のカテゴリーを追加する
		$result = array_merge($subjectes, $default);
		
		return $result;
	}
	
}



