<?php
/**
 * カテゴリーに関する定数
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Data
 * @subpackage Constant
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco_Data
 * @subpackage Constant
 * @author     charlesvineyard
 */
class Setuco_Data_Constant_Category
{
    /**
     * 未分類の文字列
     *
     * @var string
     */
    const UNCATEGORIZED_STRING = '未分類';

    /**
     * 未分類カテゴリーのvalue属性
     *
     * @var string
     */
    const UNCATEGORIZED_VALUE = 'uncategorized';

    /**
     * 未分類カテゴリーのID
     *
     * @var int
     */
    const UNCATEGORIZED_ID = 0;

    /**
     * 未分類のカテゴリーデータ
     *
     * @var array
     *
     */
    private static $_unCategorizeds = array('id' => self::UNCATEGORIZED_ID, 'name' => self::UNCATEGORIZED_STRING, 'is_default' => true);


    /**
     * カテゴリー１段目の親ID
     *
     * @var int
     */
    const NO_PARENT_ID = -1;

    /**
     * 未分類のカテゴリーを取得する
     *
     * @var array
     * @return array
     * @author suzuki-mar
     */
    public static function getUnCategorizeds()
    {
        return self::$_unCategorizeds;
    }

}