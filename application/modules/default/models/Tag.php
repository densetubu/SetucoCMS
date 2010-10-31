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
	protected $_tagDao = null;
	
	/**
	 * クラス設定の初期設定をする
	 *
	 * @return void
	 * @author suzuki-mar
	 */
	public function __construct()
	{
		$this->_tagDao = new Common_Model_DbTable_Tag();
	}

	/**
	 * タグクラウドを取得する
	 *
	 * @return array タグクラウドのデータ　title id value 値を取得できなかった場合はfalse
	 * @author suzuki-mar
	 */
	public function getTagClouds()
	{
		
        //nameとタグの使用数のカウントを取得する
		$tags  = $this->_tagDao->findTagCountAndName();
		
		
		//からならfalseを返す
		if (empty($tags)) {
			return false;
		}
		
		
		//タグのカウントの配列を作成する タグが多い順
		//タグは、1番からカウントする
		$i = 1; 
		$counts = array();
		foreach ($tags as $value) {
			//同じものは配列に入れない
			if (!in_array($value['count'], $counts)) {
			     $counts[$i] = $value['count'];
                 $i++;	
			}
			
			//タグが、最大のレベルまでいったらループを終了する
			if ($i > 10) {
				break;
			}
		}
		
		//最小のカウントを取得する
		$minCount = min($counts);
		
		//タグのレベルを設定する
		foreach ($tags as &$value) {
			//$countsの最小よりも小さい場合は、最小と同じレベルにする
			if ($value['count'] < $minCount) {
				$value['level'] = 10;
			} else {
		      $searchKeys = array_search($value['count'], $counts);
		      $value['level'] = $searchKeys;
			}
		}
		unset($value);
		
		
		//更新順でソートする
		foreach ($tags as $value) {
			$dates[] = $value['update_date'];
		}
		array_multisort($dates, SORT_DESC, $tags);
		
		$result = $tags;
		
		return $result;

	}

        /**
         * 指定されたタグIDに該当するタグの情報を取得して返す。
         *
         * @param int $tagId 取得したいタグのID
         * @return array 取得したタグデータを格納した配列
         * @author akitsukada
         */
        public function findTag($tagId)
        {
            return $this->_tagDao->find($tagId)->toArray();
        }

        /**
         * ページIDで指定された記事につけられたタグの情報を返す。
         *
         * @param int $pageId タグを取得したい記事のID
         * @return array 取得したタグ情報を格納した配列
         * @author akitsukada
         */
        public function getTagsByPageId($pageId)
        {
            return $this->_tagDao->findTagByPageId($pageId)->toArray();
        }

}
