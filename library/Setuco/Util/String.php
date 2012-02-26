<?php

/**
 * 文字列に関するユーティリティ
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

    /**
     * 文字列をデリミターで区切って配列にする
     * デフォルトのデリミターは空白
     * 全角でも区切り文字にする
     *
     * @param string $string 配列にする文字列
     * @param string[option] $delimiter デフォルトは半角空白
     * @return array 文字列を配列にした物
     */
    public static function convertArrayByDelimiter($string, $delimter = " ")
    {
        $string = mb_convert_encoding($string, "UTF-8", "auto");
        $string = mb_convert_kana($string, "s", "UTF-8");
        return explode($delimter, $string);
    }

    /**
     * 文字列が空文字またはnullならばデフォルト値を取得します。
     * どちらでもなければそのままの文字列を返します。
     *
     * @param string $string 文字列
     * @param mixed  $default デフォルト値
     * @throws UnexpectedValueException $string が文字列でない場合にthrowします
     */
    public static function getDefaultIfEmpty($string, $default)
    {
        if (($string !== null) && !is_string($string)) {
            throw new UnexpectedValueException('Parameter is not string value.');
        }
        if (($string === null) || ($string === '')) {
            return $default;
        }
        return $string;
    }

    /**
     * 先頭の文字を小文字にする
     *
     * @param string $string 文字列
     * @return 先頭の文字を小文字にしたもの
     */
    public static function convertToFirstLower($string)
    {
        $firstString = substr($string, 0, 1);
        $firstString = strtolower($firstString);
        return $firstString . substr($string, 1);
    }
}
