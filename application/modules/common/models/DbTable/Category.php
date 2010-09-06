<?php
/**
 * categoryテーブルのDbTable(DAO)クラスです。
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
 * @author     charlesvineyard
 */

/**
 * @category    Setuco
 * @package     Common_Model
 * @subpackage  DbTable
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      charlesvineyard
 */
class Common_Model_DbTable_Category extends Zend_Db_Table_Abstract
{
    /**
     * テーブル名
     * 
     * @var String
     */
    protected $_name = 'category';

    /**
     * プライマリーキーのカラム名
     *
     * @var String
     */
    protected $_primary = 'id';
}

