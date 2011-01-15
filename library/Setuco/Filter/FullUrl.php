<?php
/**
 * urlの先頭にhttp:// が付いていなかったら付ける
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Filter
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco_Filter
 * @author     suzuki-mar
 */
class Setuco_Filter_FullUrl implements Zend_Filter_Interface
{
    /**
     * フィルター処理です。
     *
     * @param  string $value フィルタをかける文字列
     * @return フィルタ適用後の文字列　先頭にhttpが付いていなかったらつけます
     * @see Zend_Filter_Interface::filter()
     * @author suzuki-mar
     */
    public function filter($value)
    {
        if (!preg_match('/^http?s:\/\//', $value)) {
           $value = "http://{$value}";
        }

        return $value;
    }
}