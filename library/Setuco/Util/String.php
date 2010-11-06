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
     * CSV文字列を分割し、配列に変換します。
     *
     * 文字列の前後の空白は削除されます。
     * 分割後の文字列が空文字の場合は戻り値に含まれません。
     * 例) splitCsvString('a, b ,,c,')
     *    → [0] => 'a'
     *      [1] => 'b'
     *      [2] => 'c'
     *
     * @param string $csvString カンマ区切りの文字列
     * @return array 分割された文字列
     * @author charlesvineyard
     */
    public static function splitCsvString($csvString)
    {
        if ($csvString === null) {
            return null;
        }
        if (!is_string($csvString)) {
            return null;
        }
        $csvString = trim($csvString);
        if ($csvString === '') {
            return null;
        }
        $strings = explode(',', $csvString);
        $result = array();
        foreach ($strings as $string) {
            $trimed = trim($string);
            if ($trimed !== '') {
                $result[] = $trimed;
            }
        }
        return $result;
    }
}
