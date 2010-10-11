<?php
/**
 * 閲覧側のタグ管理用サービス
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
 * タグ管理サービス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar
 */
class Default_Model_Tag
{
	/**
	 * タグの絶対値 
	 * Zend_Tagの関係で定義している
	 *
	 * @var array
	 */
	protected $_tagSpread = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
	
	/**
	 * モデルが使用するDAO(DbTable)クラスを設定する
	 * 
	 * @var Zend_Db_Table
	 */
	protected $_dao = null;
	
	/**
	 * クラス設定の初期設定をする
	 *
	 * @return void
	 * @author suzuki-mar
	 */
	public function __construct()
	{
		$this->_dao = new Common_Model_DbTable_Tag();

	}

	/**
	 * タグクラウドを取得する
	 *
	 * @return array タグクラウドのデータ　title id value 値を取得できなかった場合はfalse
	 * @author suzuki-mar
	 */
	public function getTagCloud()
	{
		
        //nameとタグの使用数のカウントを取得する
		$searchedTags  = $this->_dao->findTagCount();
		
		//からならfalseを返す
		if (empty($searchedTags)) {
			return false;
		}
		
		
		//検索したタグをZend_Tagで使用できる方に整形する
		foreach ($searchedTags as $value) {
			$tag['title']    = $value['name'];
			$tag['weight']   = $value['count'];
			$tag['id']       = $value['id'];
		
			//idを添字にする
			$index = $value['id'];
			$tags[$index] = $tag;
			unset($tag);
		} 
		
		
		//タグクラウドの変数を作成する
		$list     = new Zend_Tag_ItemList();
		foreach ($tags as $tag) {
			$tagItem   = new Zend_Tag_Item($tag);
			$tagItem->setParam('id', $tag['id']);
			$list[]   = $tagItem;
		}

		//絶対値の設定
		$list->spreadWeightValues($this->_tagSpread);

		//操作しやすいように配列にする
		foreach ($list as $item) {
			$tag['title'] = $item->getTitle();
			$tag['value'] = $item->getParam('weightValue');
			$tag['id']    = $item->getParam('id');
			$result[]     = $tag;
			unset($tag);
		}

		return $result;

	}

}
