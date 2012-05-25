<?php
/**
 * SetucoCMS用のテストクラスのUtilクラス
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
 * @category    Setuco
 * @package     Setuco
 * @subpackage Test
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki-mar
 */

/**
 * @package     Setuco
 * @author      suzuki-mar
 * @subpackage  Test
 */

class Setuco_Test_Util
{
    /**
     * テストで使用するファイルを初期化する
     *
     * @author suzuki-mar
     */
    public static function initFile()
    {
        $fileInit = new Dev_Model_FileInitialization();
        $fileInit->deleteUploadFile();
        $fileInit->copyFixtureFile();
    }


    /**
     * DBを初期化する
     *
     * @author suzuki-mar
     */
    public static function initDb()
    {
        $dbInitialization = new Dev_Model_DbInitialization(Setuco_Db_ConnectionFactory::create('test'));
        $dbInitialization->truncateAllTables();
        $dbInitialization->loadAllFixtureDatas();
    }

}

