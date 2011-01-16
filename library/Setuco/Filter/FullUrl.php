<?php
/**
 * 文字列の先頭にhttp:// が付いていなかったら付ける
 * 文字がからの場合はhttp://をつけない
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
 * @author     suzuki-mar
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
     * @return string 先頭にhttpが付いていなかったらつけます
     * @see Zend_Filter_Interface::filter()
     * @author suzuki-mar
     */
    public function filter($value)
    {
        $removeFilter = new Setuco_Filter_RemoveSpace();
        $value = $removeFilter->filter($value);

        if (empty($value)) {
            return $value;
        }

        //マッチしたのがなければ、http://を付加する
        $matches[] = preg_match('/^http:\/\//', $value);
        $matches[] = preg_match('/^https:\/\//', $value);
        
        if (!in_array(true, $matches)) {
           $value = "http://{$value}";
        }

        return $value;
    }
}