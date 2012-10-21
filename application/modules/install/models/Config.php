<?php

/**
 * 設定ファイルに関するクラス
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
 * @package    Install
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @since      File available since Release 1.6.1
 * @author     Takayuki Otake
 */

/**

/**
 * Configuration file class.
 *
 * @author Takayuki Otake
 */
class Install_Model_Config
{

    /**
     * 設定ファイルの書き換えをするメソッド
     *
     * @param $params array String
     * @author Takayuki Otake
     * @todo throw Exception at 'return false'
     */
    public function updateApplicationConfig($params = array())
    {
        if (empty($params) || !is_array($params)) {
            return false;
        }

        $fhr = fopen(APPLICATION_PATH . '/configs/application-sample.ini', 'r');
        $fhw = fopen(APPLICATION_PATH . '/configs/application.ini', 'w');
        while ($line = fgets($fhr)){
            $key = '';
            if (preg_match(
                    "/resources\.db\.params\.(.*?)(\s+)?\=(\s+)?\"(.*?)\"/", 
                    $line, $matches)){

                if ($matches[1] === 'host'){
                    $key = 'db_host';
                } elseif ($matches[1] === 'username') {
                    $key = 'db_user';
                } elseif ($matches[1] === 'password') {
                    $key = 'db_pass';
                } elseif ($matches[1] === 'dbname') {
                    $key = 'db_name';
                }

                if (!empty($key)){
                    $line = str_replace($matches[4], $params[$key], $line);
                }

            }
            fwrite($fhw, $line);
        }
        fclose($fhr);
        fclose($fhw);
    }

}
