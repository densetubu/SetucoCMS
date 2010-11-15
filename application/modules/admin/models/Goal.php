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
    public function loadGoalPageCountThisMonth()
    {
        $lastGoal = $this->_goalDao->findLastGoal();
        if(! $this->_isGoalOfThisMonth($lastGoal)) {
            $this->_fillGoalUntilNow($lastGoal);
            $lastGoal = $this->_goalDao->findLastGoal();
        }
        return $lastGoal['page_count'];
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
        $thisMonth = $thisMonth->toString('YYYY-MM-dd');
        for ($fillingGoal = $lastGoal; $fillingGoal['target_month'] !== $thisMonth; $lastGoal = $fillingGoal) {
            $lastGoalDate = new Zend_Date($lastGoal['target_month'], 'YYYY-MM-dd');
            $fillingGoal['target_month'] = $lastGoalDate->addMonth(1)->toString('YYYY-MM-dd');
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
    
    /**
     * 今日の時点での目標作成ページ数を求めます。
     * 
     * @param  int $lastGoalPageCount 今月の目標作成ページ数
     * @return int 今日の目標作成ページ数
     * @author charlesvineyard
     */
    public function calcTodayGoal($lastGoalPageCount)
    {
        if ($lastGoalPageCount === 0) {
            return 0;
        }
        $now = new Zend_Date();
        $daysForOnePage = $now->get(Zend_Date::MONTH_DAYS) / $lastGoalPageCount;       // 1ページ更新するための目標日数 float値
        $today = $now->get(Zend_Date::DAY_SHORT);
        return  (int) ($today / $daysForOnePage);
    }
}
