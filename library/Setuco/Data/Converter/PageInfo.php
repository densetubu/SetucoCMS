<?php
/**
 * ページ情報のデータの変換器
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
class Setuco_Data_Converter_PageInfo
{
    /**
     * ページの状態を文字列に変換します。
     *
     * @param  int $int ページの状態（Setuco_Data_Constant_Pageの定数）
     * @return string 状態の文字列
     * @author charlesvineyard
     */
    public static function convertStatus2String($int)
    {
        switch($int) {
            case Setuco_Data_Constant_Page::STATUS_DRAFT:
                return Setuco_Data_Constant_Page::STATUS_DRAFT_STRING;
            case Setuco_Data_Constant_Page::STATUS_RELEASE:
                return Setuco_Data_Constant_Page::STATUS_RELEASE_STRING;
        }
    }
}