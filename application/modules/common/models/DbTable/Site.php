<?php
/**
 * siteテーブルのDbTable(DAO)クラスです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Common
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      suzuki-mar
 */
class Common_Model_DbTable_Site extends Zend_Db_Table_Abstract
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected $_name = 'site';

    /**
     * プライマリーキーのカラム名
     *
     * @var string
     */
    protected $_primary = 'id';
    
    /**
     * サイト情報を取得する
     * 
     * @return array　サイト情報
     */
    public function findSiteInfo()
    {
    	//レコードはひとつしかない
    	$select = $this->select();
    	$searchResult = $this->fetchAll($select)->toArray();
    	$result = $searchResult['0'];
    	return $result;
    	
    }

    
}

