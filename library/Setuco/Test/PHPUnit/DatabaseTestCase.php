<?php
/**
 * SetucoCMS用にZend_Test_PHPUnit_DatabaseTestCaseを継承したクラスです
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
 * @subpackage Test_PHPUnit
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
 * @subpackage  Test_PHPUnit
 */

class Setuco_Test_PHPUnit_DatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
{
        protected $_connectionMock = null;

    protected function getConnection()
    {
        if ($this->_connectionMock == null) {

            $params = array(
                'host'      => 'localhost',
                'username'  => 'setuco',
                'password'  => 'setuco',
                'dbname'    => 'setucocms_test'

            );
            $connection = Zend_Db::factory('PDO_MYSQL', $params);

            $this->_connectionMock = $this->createZendDbConnection($connection, 'zfunittests');

            Zend_Db_Table_Abstract::setDefaultAdapter($connection);

        }

        return $this->_connectionMock;
    }

    protected function getDataSet()
    {
        $dataset = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
        $dataset->addTable('account', '/Users/suzukimasayuki/project/setucodev/tests/data/fixtures/accounts.csv');

        return $dataset;
    }

}

