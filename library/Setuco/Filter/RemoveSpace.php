<?php
/**
 * スペース（半角、全角)を削除します。
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
 * @author     suzuki-mar
 */

/**
 * @package    Setuco
 * @subpackage    Filter
 * @author     suzuki-mar
 */
class Setuco_Filter_RemoveSpace implements Zend_Filter_Interface
{
    /**
     * フィルター処理です。
     *
     * @param  string $value フィルタをかける文字列
     * @return string スペースを削除します。
     * @see Zend_Filter_Interface::filter()
     * @author suzuki-mar
     */
    public function filter($value)
    {

        $value = str_replace('　', '', $value);
        $result = str_replace(' ', '', $value);
        return $result;
    }
}