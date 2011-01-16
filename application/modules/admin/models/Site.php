<?php

/**
 * サイト情報管理サービス
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
 * @author     ece_m charlesvineyard
 */

/**
 * サイト情報管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     ece_m charlesvineyard
 */
class Admin_Model_Site extends Common_Model_SiteAbstract
{
    /**
     * ページDAO
     *
     * @var Common_Model_DbTable_Page
     */
    private $_pageDao;

    /**
     * ページサービス
     *
     * @var Admin_Model_Page
     */
    private $_pageService;

    /**
     * 目標サービス
     *
     * @var Admin_Model_Goal
     */
    private $_goalService;

    /**
     * 月初めの更新目標の最大日数
     *
     * @var int
     */
    const FIRST_UPDATE_STATUS_MAX_DAYS = 7;

    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_siteDao = new Common_Model_DbTable_Site();
        $this->_pageDao = new Common_Model_DbTable_Page();
        $this->_pageService = new Admin_Model_Page();
        $this->_goalService = new Admin_Model_Goal();
    }

    /**
     * サイトの情報を更新する
     *
     * @param array 更新するデータ
     * @return int 何件更新したのか
     * @throws update文に失敗したら例外を発生させる
     * @author suzuki-mar
     */
    public function updateSite($updateData)
    {
        //データは1件しかないないので、whereはいらない
        return $this->_siteDao->update($updateData);
    }

    /**
     * サイトの更新状況を取得します。
     *
     * @return int 更新状況(Setuco_Data_Constant_UpdateStatus)
     * @author charlesvineyard
     */
    public function getUpdateStatus()
    {
        $thisMonthGoal = $this->_goalService->findGoalPageCountThisMonth();
        $createdPageCount = $this->_pageService->countPagesCreatedThisMonth();

        if ($createdPageCount >= $thisMonthGoal) {
            return Setuco_Data_Constant_UpdateStatus::GOOD;
        }

        $todayGoal = 0;
        $now = new Zend_Date();
        $today = $now->get(Zend_Date::DAY_SHORT);
        // 1ページ更新するための目標日数 float値
        $daysForOnePage = $now->get(Zend_Date::MONTH_DAYS) / $thisMonthGoal;
        $todayGoal = (int) ($today / $daysForOnePage);

        if ($todayGoal == 0) {    //最初の日割り目標日までの間
            if ($today <= self::FIRST_UPDATE_STATUS_MAX_DAYS) {
                return Setuco_Data_Constant_UpdateStatus::FIRST;
            }
            if ($createdPageCount == 0) {    //月初めを過ぎて更新なしの場合
                return Setuco_Data_Constant_UpdateStatus::BAD;
            }
        }

        if ($todayGoal == $createdPageCount) {
            return Setuco_Data_Constant_UpdateStatus::NORMAL;
        }

        if ($todayGoal < $createdPageCount) {    // 月目標を越えている場合も入る
            return Setuco_Data_Constant_UpdateStatus::GOOD;
        }

        return Setuco_Data_Constant_UpdateStatus::BAD;
    }

    /**
     * 最終更新日(ページの最終公開日)とその日からの経過日数を取得します。
     *
     * @return array 最終更新日(lastUpdateDate Zend_Date)と経過日数(pastDays int)の配列 登録されていない場合は、false
     * @author charlesvineyard suzuki-mar
     */
    public function getLastUpdateDateWithPastDays()
    {
        $lastCreatedPage = array_pop($this->_pageDao->loadLastCreatedPages(1));

        //登録されていない場合は、falseを返す
        if (empty($lastCreatedPage)) {
            return false;
        }

        $lastUpdateDate = new Zend_Date($lastCreatedPage['create_date'], 'YYYY-MM-dd');
        return array(
            'lastUpdateDate' => $lastUpdateDate,
            'pastDays'       => Setuco_Util_Date::calcPastDays($lastUpdateDate, new Zend_Date())
        );
    }

    /**
     * サイト開設日とその日からの経過日数を取得します。
     *
     * @return array サイト開設日(openDate Zend_Date)と経過日数(pastDays int)の配列
     * @author charlesvineyard
     */
    public function getOpenDateWithPastDays()
    {
        $site = $this->getSiteInfo();
        $openDate = new Zend_Date($site['open_date'], 'YYYY-MM-dd');
        return array('openDate' => $openDate,
            'pastDays' => Setuco_Util_Date::calcPastDays($openDate, new Zend_Date()));
    }

}
