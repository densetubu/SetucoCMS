<?php
/**
 * 同じキーワードを除外する
 * 例えば、hoge,fuga,hogeだったら、hoge,fugaとなる
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
class Setuco_Filter_DeselectSameKeyword implements Zend_Filter_Interface
{
    /**
     * フィルター処理です。
     *
     * @param  string $value フィルタをかける文字列
     * @return フィルタ適用後の文字列　同じキーワードを除外した物
     * @see Zend_Filter_Interface::filter()
     * @author suzuki-mar
     */
    public function filter($value)
    {
        $keywordLists = explode(',', $value);

        $deselectedKeywordLists = array();
        foreach ($keywordLists as $value) {
            if (!in_array($value, $deselectedKeywordLists)) {
                $deselectedKeywordLists[] = $value;
            }
        }

        $result = '';
        foreach ($deselectedKeywordLists as $value) {
            $result .= $value . ',';
        }

        $result = substr($result, 0, -1);
        

        return $result;
    }
}