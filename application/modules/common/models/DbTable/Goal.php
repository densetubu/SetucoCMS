<?php
/**
 * goalテーブルのDbTable(DAO)クラスです。
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
 * @author      suzuki_mar
 */
class Common_Model_DbTable_Goal extends Zend_Db_Table_Abstract
{
    /**
     * テーブル名
     *
     * @var string
     */
    protected $_name = 'goal';

    /**
     * プライマリーキーのカラム名
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * 設定済みの中で最新の目標を取得します。
     *
     * @return array 目標情報
     * @author charlesvineyard
     */
    public function loadLastGoal()
    {
        $row = $this->fetchRow(null, "target_month DESC");
        if ($row == null) {
            return null;
        }
        return $row->toArray();
    }
}

