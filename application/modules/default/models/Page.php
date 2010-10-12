<?php
/**
 * 閲覧側のページ情報管理用サービス
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
 * ページ情報管理クラス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar
 */
class Default_Model_Page
{
	/**
     * モデルが使用するDAO(DbTable)クラスを設定する
     * 
     * @var Zend_Db_Table
     */
    protected $_dao = null;
	
    /**
     * 新着記事で標準何件取得するか
     */
    const GET_PAGE_COUNT = 10;
    
	/**
	 * 初期設定をする
	 *
	 * @author suzuki_mar
	 */
	public function __construct()
	{
		$this->_dao = new Common_Model_DbTable_Page();
	}

	/**
	 * 最新の記事を取得する
	 * 
	 * @param int[option] 何件のデータを取得するのか　標準は10件
	 * @author suzuki-mar
	 */
	public function getNewPages($getPageCount = self::GET_PAGE_COUNT) 
	{
	   $result = $this->_dao->findNewPages($getPageCount);
        
	   //からならfalseを返す
	   if (empty($result)) {
	   	return false;
	   }
	   
	   
	   return $result;
	}
}



