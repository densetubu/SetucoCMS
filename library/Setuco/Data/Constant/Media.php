<?php
/**
 * ファイルに関する定数
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
 * @subpackage Data_Constant
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco
 * @subpackage Data_Constant
 * @author     akitsukada
 */
class Setuco_Data_Constant_Media
{
    /**
     * ファイル保存ディレクトリのbaseUrl用パス。
     */
    const UPLOAD_DIR_PATH_FROM_BASE = '/media/upload/';

    /**
     * サムネイル保存ディレクトリのbaseUrl用パス。
     */
    const THUMB_DIR_PATH_FROM_BASE = '/media/thumbnail/';

    /**
     * 絞り込み処理で使うファイル種別「全て」のSelectBoxでのインデックス
     */
    const FILEEXT_ALL_INDEX = -1;

    /**
     * 絞り込み処理で使うファイル種別「全て」のSelectBoxでの値
     */
    const FILEEXT_ALL_VALUE = 'all';

    /**
     * 絞り込み処理で使うファイル種別「全て」のSelectBoxでの表示文字列
     */
    const FILEEXT_ALL_STRING = '--指定なし--';

    /**
     * サムネイルの標準表示幅
     */
    const THUMB_WIDTH = 65;

    /**
     * SetucoCMSで対応するファイル種類（拡張子）のうち、画像の拡張子のみを得る
     *
     * @return array SetucoCMSで対応する画像の拡張子の配列
     * @author akitsukada
     */
    public static function IMAGE_FILE_EXTENSIONS()
    {
        return array('jpg', 'gif', 'png');
    }

    /**
     * SetucoCMSで対応するファイル種類（拡張子）を得る
     *
     * @return array SetucoCMSで対応する拡張子の配列
     * @author akitsukada
     */
    public static function VALID_FILE_EXTENSIONS()
    {
        return array_merge(self::IMAGE_FILE_EXTENSIONS(), array('pdf', 'txt'));
    }

    /**
     * ファイルのアップロード先ディレクトリのフルパスを得る
     *
     * @return string ファイル(サムネイルではない)のアップロード先ディレクトリ名
     * @author akitsukada
     */
    public static function MEDIA_UPLOAD_DIR_FULLPATH()
    {
        return APPLICATION_PATH . '/../public/media/upload';
    }

    /**
     * サムネイルの格納先ディレクトリのフルパスを得る
     *
     * @return string サムネイルの格納先ディレクトリ名
     * @author akitsukada
     */
    public static function MEDIA_THUMB_DIR_FULLPATH()
    {
        return APPLICATION_PATH . "/../public/media/thumbnail";
    }


}