<?php

/**
 * 設定ファイルに関するユーティリティ
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
 * @package     Setuco
 * @subpackage  Util
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 1.6.0
 * @author     Takayuki Otake
 */

/**
 * @package     Setuco
 * @subpackage  Util
 * @author      Takayuki Otake
 */
class Setuco_Util_Config
{

    /**
     * 設定ファイルのディレクトリが書き込み可能であるかを判定する
     *
     * @return boolean 設定ファイルのディレクトリが書き込み可能か
     * @author Takayuki Otake
     */
    public static function isWritableConfigDir()
    {
        $dir = Setuco_Data_Constant_Config::Config_DIR_FULLPATH();
        if (!is_dir($dir)) {
            return false;
        }
        if (!is_writable($dir)) {
            return false;
        }
        return true;
    }

}
