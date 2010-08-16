<?php
/**
 * siteテーブルのDbTable(DAO)クラスです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @package    Common_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

class Common_Model_DbTable_Site extends Zend_Db_Table_Abstract
{
    /**
     * テーブル名
     * 
     * @var String
     */
    protected $_name = 'site';

    /**
     * プライマリーキーのカラム名
     *
     * @var String
     */
    protected $_primary = 'name';
}

