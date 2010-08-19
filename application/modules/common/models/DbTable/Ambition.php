<?php
/**
 * ambitionテーブルのDbTable(DAO)クラスです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Common_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */



/**
 * @category    Setuco
 * @package     Common_Model
 * @subpackage  DbTable
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      suzuki-mar
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
    protected $_primary = 'ambition';
}

