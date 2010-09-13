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
     * サイト情報を取得する
     *
     * @return array サイト情報
     */
    public function getSiteInfo()
	{
		$result = array('name' => '日本電子専門学校 電設部?',
						'url' => 'http://design1.chu.jp/testsetuco/penguin/',
						'comment' => '日本電子専門学校電設部SetucoCMSプロジェクトです。',
						'keyword' => 'せつこ,俺だ,結婚,してくれ');
		return $result;
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
     * @return array 最終更新日と経過日数の配列
     * @author charlesvineyard
     */
    public function getLastUpdateDateWithPastDays()
    {
        $lastUpdateDate = new Zend_Date();
        $lastUpdateDate->setDate('2010-09-10', 'YYYY-MM-dd', 'ja_JP');
        return array('lastUpdateDate' => $lastUpdateDate,
                     'pastDays' => 10);
    }

    /**
     * サイト開設日とその日からの経過日数を取得します。
     *
     * @return array サイト開設日と経過日数の配列
     * @author charlesvineyard
     */
    public function getOpenDateWithPastDays()
    {
        $openDate = new Zend_Date();
        $openDate->setDate('2009-09-01', 'YYYY-MM-dd', 'ja_JP');
        return array('openDate' => $openDate,
                     'pastDays' => 300);
    }

}

