<?php
/**
 * カテゴリー情報のデータの変換器
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Data
 * @subpackage Converter
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco_Data
 * @subpackage Converter
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
    public static function convertCategoryID4View($categoryId)
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
    public static function convertCategoryID4Data($categoryId)
    {
        // 未分類
        if ($categoryId === Setuco_Data_Constant_Category::UNCATEGORIZED_VALUE) {
            return null;
        }

        return $categoryId;
    }
}