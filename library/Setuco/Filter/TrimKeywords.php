<?php
/**
 * キーワード(区切り文字)ごとに前後のスペースを削除する
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
class Setuco_Filter_TrimKeywords implements Zend_Filter_Interface
{
    /**
     * フィルター処理です。
     *
     * @param  string $value フィルタをかける文字列
     * @return strig フィルタ適用後の文字列　キーワードごとに前後のスペースを削除する
     * @see Zend_Filter_Interface::filter()
     * @author suzuki-mar
     */
    public function filter($value)
    {
        $keywordLists = explode(',', $value);

        $result = '';
        foreach ($keywordLists as $value) {
            $result .= trim($value);
            $result .= ',';
        }

        $result = substr($result, 0, -1);

        return $result;
    }
}