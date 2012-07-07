<?php
/**
 * connectionインスタンスを作成するクラスです。
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
 * @subpackage Db_Table
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * @package    Setuco
 * @subpackage Db_Table
 * @author      charlesvineyard
 */
class Setuco_Db_ConnectionFactory
{

    public static function create($environment = "development")
    {
        $configPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini';
        $applicationConfig = new Zend_Config_Ini($configPath);
        $dbConfig = $applicationConfig->testing->resources->db;
        $dbName   = $dbConfig->params->dbname;

        if ($environment === 'test') {
            if (!preg_match("/_test/", $dbName)) {
                $dbName .= '_test';
            }
        }


        $params = array(
            'host'      => $dbConfig->params->host,
            'username'  => $dbConfig->params->username,
            'password'  => $dbConfig->params->password,
            'dbname'    => $dbName,

        );

        $adapterName = strtoupper($dbConfig->adapter);
        return Zend_Db::factory($adapterName, $params);
    }

}
