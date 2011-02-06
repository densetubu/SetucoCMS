<?php
/**
 * URLデコードするフィルタ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage    Filter
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco
 * @subpackage    Filter
 * @author     charlesvineyard
 */
class Setuco_Filter_UrlDecode implements Zend_Filter_Interface
{
    /**
     * フィルター処理です。
     *
     * @param  string $value フィルタをかける文字列
     * @return フィルタ適用後の文字列
     * @see Zend_Filter_Interface::filter()
     * @author charlesvineyard
     */
    public function filter($value)
    {
        return urldecode($value);
    }
}