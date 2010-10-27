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
class Admin_Model_Site
{
    /**
     * サイトDAO
     *
     * @var Common_Model_DbTable_Site
     */
    private $_siteDao;
    
    /**
     * ページDAO
     * 
     * @var Common_Model_DbTable_Page
     */
    private $_pageDao;
    
    /**
     * コンストラクター
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_siteDao = new Common_Model_DbTable_Site();
        $this->_pageDao = new Common_Model_DbTable_Page();
    }
    
    /**
     * サイトの情報を更新する
     * 
     * @param array 更新するデータ
     * @return boolean 更新に成功したか
     * @author suzuki-mar
     */
    public function updateSite($inputData)
    {
    	$updateData = $inputData;
    	unset($updateData['module'], $updateData['controller'], $updateData['action'], 
            $updateData['sub']);
    	
        //アップデートに失敗したときに例外が発生する
        try {

        	//データは1件しかないないので、whereはいらない
            $this->_siteDao->update($updateData, true);
            $result     = true;

        } catch (Zend_Exception $e) {
            $result = false;            
        }

        return $result;
    }
    	
    
    
    /**
     * サイト情報を取得する
     *
     * @return array サイト情報
     */
    public function getSiteInfo()
    {
        return $this->_siteDao->fetchRow()->toArray();
    }

    /**
     * サイトの更新状況を取得します。
     *
     * @return int 更新状況
     * @author charlesvineyard
     */
    public function getUpdateStatus()
    {
        // TODO
        return Setuco_Data_Constant_UpdateStatus::FIRST;
    }

    /**
     * 最終更新日とその日からの経過日数を取得します。
     *
     * 最終更新日はページの公開日時が最新のものです。
     *
     * @return array 最終更新日(lastUpdateDate Zend_Date)と経過日数(pastDays int)の配列
     * @author charlesvineyard
     */
    public function getLastUpdateDateWithPastDays()
    {
        $newPages = $this->_pageDao->findLastUpdatePages(1);    // 二次元配列で返ってくる
        $lastUpdateDate = new Zend_Date();
        $lastUpdateDate->setDate($newPages[0]['create_date'], 'YYYY-MM-dd', 'ja_JP');
        return array('lastUpdateDate' => $lastUpdateDate,
                     'pastDays' => $this->_findPastDays($lastUpdateDate, new Zend_Date()));
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
        $openDate = new Zend_Date();
        $openDate->setDate($site['open_date'], 'YYYY-MM-dd', 'ja_JP');
        return array('openDate' => $openDate,
                     'pastDays' => $this->_findPastDays($openDate, new Zend_Date()));
    }
    
    /**
     * ある日付から他の日付までの経過日数を求めます。
     * 
     * 引数の日付のHOUR以下の設定は切り捨てて計算します。
     * $toDateが$fromDateより小さい場合はマイナス値が返ります。
     * 
     * @param  Zend_Date $fromDate 経過日数の起算日
     * @param  Zend_Date $toDate   経過日数の終算日
     * @return int 経過日数
     * @author charlesvineyard
     */
    private function _findPastDays($fromDate, $toDate)
    {
        $fromDate->setTime('00:00:00', 'HH:mm:ss', 'ja_JP');
        $toDate->setTime('00:00:00', 'HH:mm:ss', 'ja_JP');
        $pastDaysValue = $toDate->toValue() - $fromDate->toValue();
        return (int)($pastDaysValue / 60 / 60 / 24);
    }

}
