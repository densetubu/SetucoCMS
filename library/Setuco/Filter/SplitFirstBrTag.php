<?php
/**
 * 行頭がbrタグで1つのbrタグだけの場合に取り除くフィルター
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
 * @author     charlesvineyard
 */
class Setuco_Filter_SplitFirstBrTag implements Zend_Filter_Interface
{
    /**
     * フィルター処理です。行頭がbrタグで1つのbrタグだけの場合に取り除く。
     *
     * @param  string $value フィルタをかける文字列
     * @return フィルタ適用後の文字列
     * @see Zend_Filter_Interface::filter()
     * @author charlesvineyard
     */
    public function filter($value)
    {
        return preg_replace('/^<br[^>]*>$/i', '', $value);
    }
}