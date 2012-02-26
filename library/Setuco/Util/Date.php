<?php
/**
 * 日付に関するユーティリティ
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
 * @package     Setuco
 * @subpackage  Util
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package     Setuco
 * @subpackage  Util
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
