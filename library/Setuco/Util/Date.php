<?php
/**
 * 日付に関するユーティリティ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Util
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco_Util
 * @author     charlesvineyard
 */
class Setuco_Util_Date
{
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
    public static function calcPastDays(Zend_Date $fromDate, Zend_Date $toDate)
    {
        $fromDate->setTime('00:00:00', 'HH:mm:ss');
        $toDate->setTime('00:00:00', 'HH:mm:ss');
        $pastDaysValue = $toDate->toValue() - $fromDate->toValue();
        return (int)($pastDaysValue / 60 / 60 / 24);
    }
}
