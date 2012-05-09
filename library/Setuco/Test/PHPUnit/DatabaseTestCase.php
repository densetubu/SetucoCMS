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
     * フィクスチャーから値を作成するクラス
     *
     * @var CreateExpected
     * @todo 名前を変更したほうがいいかも
     */
     protected $_createExpected = null;

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

        if (is_null($this->_createExpected)) {
            $createExcepetedPath = self::FIXTURE_BASE_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'CreateExpected.php';
            require_once $createExcepetedPath;

            $this->_createExpected = new CreateExpected();
        }
        
    }

    protected function getConnection()
    {

        if ($this->_connectionMock == null) {
            $connection = $this->_createDbConnectionInstance();
            $this->_connectionMock = $this->createZendDbConnection($connection, 'zfunittests');

            Zend_Db_Table_Abstract::setDefaultAdapter($connection);
        }

        return $this->_connectionMock;
    }

    /**
     * dbコネクションインスタンスを作成する
     *
     * @return Zend_Db_Adapter_Pdo_Abstract
     */
     private function _createDbConnectionInstance()
     {
         
        $configPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini';
        $applicationConfig = new Zend_Config_Ini($configPath);
        $dbConfig = $applicationConfig->testing->resources->db;
        $dbName   = $dbConfig->params->dbname . '_test';


        $params = array(
            'host'      => $dbConfig->params->host,
            'username'  => $dbConfig->params->username,
            'password'  => $dbConfig->params->password,
            'dbname'    => $dbName,

        );

        $adapterName = strtoupper($dbConfig->adapter);
        $connection = Zend_Db::factory($adapterName, $params);
        
        return $connection;
     }


    protected function getDataSet()
    {
        $dataset = new Setuco_Test_PHPUnit_DataSet();

        $globPattern = self::FIXTURE_BASE_PATH . '*.php';

        foreach (glob($globPattern) as $filePath) {
            $baseFile = basename($filePath);
            $tableNames[] = str_replace('.php', '', $baseFile);
        }

        $dataset->addTables($tableNames);

        
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

