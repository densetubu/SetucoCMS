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
    protected $_isDataSetup = false;

    /**
     * フィクスチャーのベースパス
     */
    const FIXTURE_BASE_PATH = '/Users/suzukimasayuki/project/setucodev/tests/data/fixtures/';

    /**
     * fixtureを読み込んだか
     *
     * @var boolean
     */
    protected $_isLoadFixture = false;

    protected function setup()
    {
        if (!$this->_isLoadFixture) {
            parent::setUp();
            $this->_isLoadFixture = true;
        }
    }

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

        $globPattern = self::FIXTURE_BASE_PATH . '*';

        foreach (glob($globPattern) as $filePath) {

            $extension = pathinfo($filePath, PATHINFO_EXTENSION);

            if ($extension === 'csv') {
                $baseFile = basename($filePath);
                $tableName = str_replace('.csv', '', $baseFile);
                $dataset->addTable($tableName, $filePath);
            } else {
                require_once $filePath;
            }

        }

        return $dataset;
    }

    /**
     * DBのレコード配列を比較する
     *
     * @param array $expected 期待値
     * @param array $actual  実際の値
     */
    protected function assertRowDatas($expected, $actual)
    {
        $expected = $this->_sortRowsById($expected);
        $actual = $this->_sortRowsById($actual);

        return $this->assertEquals($expected, $actual);
    }

    /**
     * DBのレコード配列をIDの昇順でソートする
     *
     * @param array $rows ソートするID
     * @return IDの昇順でソートしたデータ
     * @author suzuki-mar
     */
    protected function _sortRowsById($rows)
    {
        foreach ($rows as $key => $row) {
            $ids[$key] = $row['id'];
        }

        array_multisort($rows, SORT_ASC, $ids);

        return $rows;
    }

}
