<?php
/**
 * ファイルに関するユーティリティ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package     Setuco
 * @subpackage  Util
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package     Setuco
 * @subpackage  Util
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


    /**
     * ファイルのアップロード先ディレクトリが書き込み可能であるかを判定する
     *
     * @return boolean ファイルのアップロード先ディレクトリが書き込み可能か
     * @author akitsukada
     */
    public static function isWritableUploadDir()
    {
        $dir = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH();
        if (!is_dir($dir)) {
            return false;
        }
        if (!is_writable($dir)) {
            return false;
        }
        return true;
    }

    /**
     * サムネイルのアップロード先ディレクトリが書き込み可能であるかを判定する
     *
     * @return boolean サムネイルのアップロード先ディレクトリが書き込み可能か
     * @author akitsukada
     */
    public static function isWritableThumbDir()
    {
        $dir = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH();
        if (!is_dir($dir)) {
            return false;
        }
        if (!is_writable($dir)) {
            return false;
        }
        return true;
    }

    /**
     * 画像の拡張式を取得する
     *
     * ファイル名に関係なく正しい拡張式を取得する
     *
     * @param string $filePath 拡張式を取得するファイルタイプ
     * @return string 拡張式
     * @author suzuki-mar
     */
    public static function getImageType($imagePath)
    {
       $imageInfo = getimagesize($imagePath);
       $imageType = image_type_to_mime_type($imageInfo[2]);
       //ファイル前の種類を取り除く image/png の imageの部分
       $result = preg_replace('/^(image|application)\//', '', $imageType);
       return $result;
    }
}
