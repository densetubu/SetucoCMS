<?php
/**
 * ファイルに関するユーティリティ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Util
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco_Util
 * @author     akitsukada
 */
class Setuco_Util_Media
{
    /**
     * SetucoCMSで扱える拡張子であるかどうかを判定する
     *
     * @param string $extension 判定する拡張子
     * @return boolean 対応する拡張子ならtrue、未対応ならfalse
     */
    public static function isValidExtension($extension)
    {
        return in_array($extension, Setuco_Data_Constant_Media::VALID_FILE_EXTENSIONS(), TRUE);
    }

    /**
     * SetucoCMSで扱える画像の拡張子であるかどうかを判定する
     *
     * @param string $extension 判定する拡張子
     * @return boolean 対応する画像拡張子ならtrue、未対応ならfalse
     */
    public static function isImageExtension($extension)
    {
        return in_array($extension, Setuco_Data_Constant_Media::IMAGE_FILE_EXTENSIONS(), TRUE);
    }
}
