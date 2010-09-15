<?php
/**
 * 更新状況のデータの変換器
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Data
 * @subpackage Converter
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco_Data
 * @subpackage Converter
 * @author     charlesvineyard
 */
class Setuco_Data_Converter_UpdateStatus
{
    /**
     * 更新状況を文字列に変換します。
     *
     * @param  int $int 更新状況（Setuco_Data_Constant_UpdateStatusの定数）
     * @return string 更新状況の文字列
     * @author charlesvineyard
     */
    public static function convertUpdateStatus2String($int)
    {
        switch($int) {
            case Setuco_Data_Constant_UpdateStatus::FIRST:
                return '今月も頑張ろう';
            case Setuco_Data_Constant_UpdateStatus::NORMAL:
                return '普通';
            case Setuco_Data_Constant_UpdateStatus::GOOD:
                return '良い';
            case Setuco_Data_Constant_UpdateStatus::BAD:
                return '悪い';
        }
    }
}