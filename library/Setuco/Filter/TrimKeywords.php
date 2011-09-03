<?php
/**
 * キーワード(区切り文字)ごとに前後のスペースを削除する
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