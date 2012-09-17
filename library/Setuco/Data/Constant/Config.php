<?php
/**
 * 設定ファイルに関する定数
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
 * @since      File available since Release 1.6.0
 * @author     Takayuki Otake
 */

/**
 * @package    Setuco
 * @subpackage Data_Constant
 * @author     Takayuki Otake
 */
class Setuco_Data_Constant_Config
{
    /**
     * ファイル保存ディレクトリのbaseUrl用パス。
     */
    const CONFIG_DIR_PATH_FROM_BASE = '/configs/';

    /**
     * 設定ファイルのディレクトリのフルパスを得る
     *
     * @return string 設定ファイルのディレクトリ名
     * @author Takayuki Otake
     */
    public static function CONFIG_DIR_FULLPATH()
    {
        return APPLICATION_PATH . '/configs';
    }

}
