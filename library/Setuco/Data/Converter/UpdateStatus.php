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
     * @param  int $updateStatus 更新状況（Setuco_Data_Constant_UpdateStatusの定数）
     * @return string 更新状況の文字列
     * @author charlesvineyard
     */
    public static function convertUpdateStatus2String($updateStatus)
    {
        switch($updateStatus) {
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
    
    /**
     * 目標との差分ページ数を表示用の文言に変換します。
     *
     * @param  int $diffGoal 目標との差分ページ数
     * @return string 更新状況の文字列
     * @author charlesvineyard
     */
    public static function convertDiffGoal2String($diffGoal)
    {
        if (!is_int($diffGoal)) {
            throw new Zend_Date_Exception('目標との差分ページ数が[' . $diffGoal . ']になっています。');
        }
        if (0 === $diffGoal) {
            return '目標達成！';
        }
        if (0 < $diffGoal) {
            return '目標からプラス' . $diffGoal . 'ページ！';
        }
        if (0 > $diffGoal) {
            return '目標まであと' . ($diffGoal * -1) . 'ページ！';
        }
    }
}