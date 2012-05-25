<?php

/**
 * SetucoCMS用にPHPUnit_Extensions_SeleniumTestCaseを継承したクラスです
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
 * @subpackage  Test_PHPUnit
 * @copyright   Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license     http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki-mar
 */

/**
 * @package     Setuco
 * @author      suzuki-mar
 * @subpackage  Test_PHPUnit
 */
class Setuco_Test_PHPUnit_SeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{

    private static $_loggedin = false;
    private $_resetDb = false;

    protected function setUp()
    {
        //テストケースごとにDBをリセットする
        if (!$this->_resetDb) {
            Setuco_Test_Util::initDb();
            $this->_resetDb = true;
        }

        Setuco_Test_Util::initFile();
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://setucocms.localdomain/");
    }

    protected function _login()
    {
        if (self::$_loggedin) {
            return;
        }

        $this->open("/admin/page");

        $this->open("/admin/login");
        $this->type("id=password", "password");
        $this->type("id=login_id", "admin");
        $this->click("id=sub");
        $this->waitForPageToLoad("30000");

        self::$_loggedin = true;
    }

    /**
     * データの一覧が指定した件数あるか
     * assert系のメソッドなのでアンスコは付けていない
     *
     * @param int $expectedCount　期待している行数 この数だとテスト成功
     * @author suzuki-mar
     */
    protected function assertCountItemList($expectedCount)
    {
        $actualCount = intval($this->getXpathCount("//tr[@class='list_item']"));
        $actualCount += intval($this->getXpathCount("//tr[@class='check list_item']"));
        $this->assertSame($expectedCount, $actualCount);
    }

}

