<?php
/**
 * ページ情報のデータの変換器
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
 * @subpackage Data_Converter
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco
 * @subpackage Data_Converter
 * @author     charlesvineyard
 */
class Setuco_Data_Converter_PageInfo
{
    /**
     * ページの状態を文字列に変換します。
     *
     * @param  int $int ページの状態（Setuco_Data_Constant_Pageの定数）
     * @return string 状態の文字列
     * @author charlesvineyard
     */
    public static function convertStatus2String($int)
    {
        switch($int) {
            case Setuco_Data_Constant_Page::STATUS_DRAFT:
                return Setuco_Data_Constant_Page::STATUS_DRAFT_STRING;
            case Setuco_Data_Constant_Page::STATUS_RELEASE:
                return Setuco_Data_Constant_Page::STATUS_RELEASE_STRING;
        }
    }
}