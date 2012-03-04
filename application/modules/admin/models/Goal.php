<?php
/**
 * 更新目標に関するサービス
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
    /**
     * 目標DAO
     *
     * @var Common_Model_DbTable_Ambition
     */
    private $_goalDao;

    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_goalDao = new Common_Model_DbTable_Goal();
    }

    /**
     * 当月の更新目標ページ数を取得します。
     *
     * @return int 更新目標ページ数
     * @author charlesvineyard
     */
    public function findGoalPageCountThisMonth()
    {
        $lastGoal = $this->_goalDao->loadLastGoal();
        if ($lastGoal === null) {
            throw new Setuco_Exception('目標が設定されていません。');
        }
        if(! $this->_isGoalOfThisMonth($lastGoal)) {
            $this->_fillGoalUntilNow($lastGoal);
            $lastGoal = $this->_goalDao->loadLastGoal();
        }
        return (int) $lastGoal['page_count'];
    }

    /**
     * 今月の目標かどうか判断します。
     *
     * @param  array $goal 目標情報
     * @return boolean 今月の目標なら true
     * @author charlesvineyard
     */
    private function _isGoalOfThisMonth($goal)
    {
        $goalDate = new Zend_Date($goal['target_month'], 'YYYY-MM-dd');
        $now = new Zend_Date();
        if($now->get(Zend_Date::MONTH) != $goalDate->get(Zend_Date::MONTH)) {
            return false;
        }
        return true;
    }

    /**
     * 目標が設定されていない月から今月までの目標をすべて設定します。
     *
     * @param  array $lastGoal 設定済みの中で最新の目標情報
     * @author charlesvineyard
     */
    private function _fillGoalUntilNow($lastGoal)
    {
        $thisMonth = new Zend_Date();
        $thisMonth->set(1, Zend_Date::DAY);
        $thisMonth = $thisMonth->toString('yyyy-MM-dd');
        $lastGoalDate = new Zend_Date($lastGoal['target_month'], 'yyyy-MM-dd');;
        for ($fillingGoal = $lastGoal; $fillingGoal['target_month'] !== $thisMonth; $lastGoal = $fillingGoal['target_month']) {
            $lastGoalDate->addMonth(1);
            $fillingGoal['target_month'] = $lastGoalDate->toString('yyyy-MM-dd');
            unset($fillingGoal['id']);
            $this->_goalDao->insert($fillingGoal);
        }
    }

    /**
     * 当月の更新目標ページ数を更新します。
     *
     * @param  int 当月の更新目標ページ数
     * @return void
     * @author charlesvineyard
     */
    public function updateGoalPageCountThisMonth($goalPageCount)
    {
        $thisMonth = new Zend_Date();
        $thisMonth->set(1, Zend_Date::DAY);
        $thisMonth = $thisMonth->toString('YYYY-MM-dd');
        $where = $this->_goalDao->getAdapter()->quoteInto('target_month = ?', $thisMonth);
        $this->_goalDao->update(array('page_count' => $goalPageCount), $where);
    }

}
