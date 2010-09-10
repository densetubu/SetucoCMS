<?php
/**
 * 更新目標に関するサービスです。
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
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     charlesvineyard
 */
class Admin_Model_Goal
{
    /**
     * 当月の更新目標ページ数を取得します。
     * 
     * @return int 更新目標ページ数
     * @author charlesvineyard
     */
    public function loadGoalPageCount()
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
    
    /**
     * 更新状況を取得します。
     * 
     * @return int 更新状況
     * @author charlesvineyard
     */
    public function findGoalStatus()
    {
        // TODO
        return 0;
    }
}

