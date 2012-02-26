<?php
/**
 * カテゴリー情報のデータの変換器
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
class Setuco_Data_Converter_CategoryInfo
{
    /**
     * カテゴリーIDをビュー用の値に変換します。
     *
     * @param  int $categoryId カテゴリーID
     * @return mixed カテゴリーIDの表示用の値
     * @author charlesvineyard
     */
    public static function convertCategoryId4View($categoryId)
    {
        // 未分類
        if ($categoryId === null) {
            return Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE;
        }

        return $categoryId;
    }

    /**
     * カテゴリー名をビュー用の文字列に変換します。
     *
     * @param  string $categoryName カテゴリー名
     * @return string カテゴリー名の表示用の文字列
     * @author charlesvineyard
     */
    public static function convertCategoryName4View($categoryName)
    {
        // 未分類
        if ($categoryName === null) {
            return Setuco_Data_Constant_Category::UNCATEGORIZED_STRING;
        }

        return $categoryName;
    }

    /**
     * カテゴリーIDをデータ用の値に変換します。
     *
     * @param  mixed カテゴリーIDの表示用の値
     * @return int $categoryId カテゴリーID
     * @author charlesvineyard
     */
    public static function convertCategoryId4Data($categoryId)
    {
        // 未分類
        if ($categoryId === Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE) {
            return null;
        }

        return $categoryId;
    }
}