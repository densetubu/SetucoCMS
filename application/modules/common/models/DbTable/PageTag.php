<?php
/**
 * page_tagテーブルのDbTable(DAO)クラスです。
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
 * @package     Common
 * @subpackage  Model_DbTable
 * @author      charlesvineyard
 */
class Common_Model_DbTable_PageTag extends Setuco_Db_Table_Abstract
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $_name = 'page_tag';

    /**
     * プライマリーキーのカラム名
     *
     * @var array
     */
    protected $_primary = array('page_id', 'tag_id');

}
