<?php
/**
 * SetucoCMSの単体テストを全て実行する
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
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki-mar
 */

/**
 *
 * @author      suzuki-mar
 */

require_once 'bootstrap.php';

class AllTests
{
    private static $_testDirNames = array();
    

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');

        self::_findDirNames(TEST_DIR . 'library' . DIRECTORY_SEPARATOR . 'Setuco');
        self::_findDirNames(TEST_DIR . 'application');

        foreach (self::$_testDirNames as $dirName) {

            $globPattern = $dirName . DIRECTORY_SEPARATOR . '*.php';

            foreach (glob($globPattern) as $fileName) {
                $suite->addTestFile($fileName);
            }
        }

        $suite->addTestFile("/Users/suzukimasayuki/project/setucodev/tests/library/Setuco/Sql/GeneratorTest.php");
        
        return $suite;
    }


    /**
     * 再帰的にテストディレクトリ一覧を取得する
     *
     * @param string $baseDirName テストディレクトリを探すパス
     */
    private static function _findDirNames($baseDirName)
    {
        $globPattern = $baseDirName . DIRECTORY_SEPARATOR . '*';

        foreach (glob($globPattern, GLOB_ONLYDIR) as $dirName) {
            self::$_testDirNames[] = $dirName;

            self::_findDirNames($dirName);
        }

    }

}
