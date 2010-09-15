<?php
/**
 * 文字列に関するユーティリティ
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
class Setuco_Util_String
{
    /**
     * 数の符号を文字列に変換します。
     * 
     * @param  int    $number 数
     * @return string 文字列の符号付きの数
	 * @author charlesvineyard
     */
    public static function convertSign2String($number)
    {
        if ($number >= 0) {
            return 'プラス' . $number;
        }
        return 'マイナス' . $number * -1;
    } 

}
