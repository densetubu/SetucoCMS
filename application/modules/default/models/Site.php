<?php
/**
 * 閲覧側のサイト情報管理用サービス
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
 * サイト情報管理クラス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar
 */
class Default_Model_Site
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
		$this->_dao = new Common_Model_DbTable_Site();
	}
	
	/**
	 * サイト情報を取得する
	 * 
	 * @return array サイト情報
	 * @author suzuki_mar
	 */
	public function getSiteInfos()
	{
		$result = $this->_dao->findSiteInfo();
		//開設年を取得する
		$result['start_year'] = substr($result['open_date'], 0, 4);
		return $result;
	}
	
	/**
	 * フリースペースの内容を取得する
	 * 
	 * @return String フリースペースの内容
	 * @author suzuki_mar
	 */
	public function getFreeSpace()
	{
		$siteInfo = $this->_dao->findSiteInfo();
		$result = $siteInfo['comment'];
		return $result;
		
	}
	
}



