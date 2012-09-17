<?php

/**
 * 設置環境に関するユーティリティ
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
class Setuco_Util_Environment
{

    /**
     * SetucoCMSの動作に必要なPHPモジュールのリストを返す
     *
     * @return array PHP Extensionのモジュール名
     * @author Takayuki Otake
     */
    public static function getRequiredPhpExtensions()
    {
        return array('pdo', 'mysql', 'mbstring', 'gd');
    }

    /**
     * SetucoCMSの動作に必要なApacheモジュールのリストを返す
     *
     * @return array Apacheモジュールの名前
     * @author Takayuki Otake
     */
    public static function getRequiredApacheModules()
    {
        return array('mod_rewrite');
    }

    /**
     * SetucoCMSの動作に必要なPHPモジュールがサーバーにインストールされているかをチェックする
     *
     * @return array キーにモジュール名、値にインストールされているかどうかの判定
     * @author Takayuki Otake
     */
    public static function checkRequiredPhpExtensions()
    {
        $requiredExtensions;
        foreach (self::getRequiredPhpExtensions() as $ext) {
            $requiredExtensions[$ext] = extension_loaded($ext);
        }
        return $requiredExtensions;
    }

    /**
     * SetucoCMSの動作に必要なApacheモジュールがサーバーにインストールされているかをチェックする
     *
     * @return array キーにモジュール名、値にインストールされているかどうかの判定
     * @author Takayuki Otake
     */
    public static function checkRequiredApacheModules()
    {
        $requiredModules = array();
        $loadedApacheModules = apache_get_modules();
        foreach (self::getRequiredApacheModules() as $module) {
            $requiredModules[$module] = in_array($module, $loadedApacheModules);
        }
        return $requiredModules;
    }

}
