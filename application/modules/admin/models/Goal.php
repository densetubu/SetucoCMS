<?php
/**
 * 更新目標に関するサービス
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * 更新目標管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     charlesvineyard
 */
class Admin_Model_Goal
{
    private $_goal;

    public function init()
    {
        $this->_goal = new Common_Model_DbTable_Goal();

    }
    /**
     * 当月の更新目標ページ数を取得します。
     *
     * @return int 更新目標ページ数
     * @author charlesvineyard
     */
    public function loadMonthlyGoalPageCount()
    {
        // TODO
        return 10;
    }

    /**
     * 当月の更新目標ページ数を更新します。
     *
     * @param  int 更新目標ページ数
     * @return void
     * @author charlesvineyard
     */
    public function updateGoalPageCount()
    {
        // TODO
    }

}

