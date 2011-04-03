<?php

/**
 * 文字列に関するユーティリティ
 *
 * LICENSE: ライセンスに関する情報
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

}
