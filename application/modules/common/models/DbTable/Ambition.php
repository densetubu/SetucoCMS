<?php
/**
 * ambitionテーブルのDbTable(DAO)クラスです。
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
 * @author     charlesvineyard
 */

/**
 * @package     Common_Model
 * @subpackage  DbTable
 * @author      charlesvineyard
 */
class Common_Model_DbTable_Ambition extends Zend_Db_Table_Abstract
{    
    /**
     * テーブル名
     * 
     * @var String
     */
    protected $_name = 'ambition';

    /**
     * プライマリーキーのカラム名
     *
     * @var String
     */
    protected $_primary = 'id';
}

