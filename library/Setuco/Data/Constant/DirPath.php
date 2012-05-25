<?php
/**
 * ディレクトリパスに関する定数
 *
 * ディレクトリ定義が分散しないようにするためにここに定義する
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
 * @author     suzuki-mar
 */

/**
 * @package    Setuco
 * @subpackage Data_Constant
 * @author     suzuki-mar
 */
class Setuco_Data_Constant_DirPath
{
    /**
     * ルートパス
     */
    public static function ROOT_PATH()
    {
        return realpath(APPLICATION_PATH . '/..');
    }


    /**
     * publicディレクトリのパスを取得する
     *
     * jsとかcssを置くディレクトリ
     *
     * @author suzuki-mar
     */
    public static function PUBLIC_PATH()
    {
        return self::ROOT_PATH() . '/public';
    }


    /**
     * メディアのアップロードするベースパスを取得する
     *
     * @return strng メディアのアップロードするベースとなるディレクトリパス名
     * @author suzuki-mar
     */
    public static function MEDIA_UPLOAD_BASEPATH()
    {
        return self::PUBLIC_PATH() . '/media';
    }

    /**
     * ファイルのアップロード先ディレクトリのフルパスを得る
     *
     * @return string ファイル(サムネイルではない)のアップロード先ディレクトリ名
     * @author suzuki-mar
     */
    public static function MEDIA_UPLOAD_PATH()
    {
        return self::MEDIA_UPLOAD_BASEPATH() . '/upload';
    }

    /**
     * サムネイルの格納先ディレクトリのフルパスを得る
     *
     * @return string サムネイルの格納先ディレクトリ名
     * @author suzuki-mar
     */
    public static function MEDIA_THUMB_PATH()
    {
        return self::MEDIA_UPLOAD_BASEPATH() . '/thumbnail';
    }

    /**
     * テストディレクトリのパス
     */
    public static function TEST_PATH()
    {
        return self::ROOT_PATH() . '/tests';
    }

    /**
     * フィクスチャーのパス
     */
    public static function FIXTURE_PATH()
    {
        return self::TEST_PATH() . '/data';
    }

    /**
     * ファイルのフィクスチャーのパス
     */
    public static function FIXTURE_FILE_PATH()
    {
        return self::FIXTURE_PATH() . '/file';
    }

    /**
     * mediaのファイルのフィクスチャーのパス
     */
    public static function FIXTURE_FILE_MEDIA_PATH()
    {
        return self::FIXTURE_FILE_PATH() . '/media';
    }

    /**
     * templateのファイルのフィクスチャーのパス
     */
    public static function FIXTURE_FILE_TEMPLATE_PATH()
    {
        return self::FIXTURE_FILE_PATH() . '/template';
    }


}