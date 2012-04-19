<?php
/**
 * クラスベースで使用できるDataSetクラスです
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

class Setuco_Test_PHPUnit_DataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
    /**
     * DataSet_AbstractDataSetの変数なので名前はそのまま
     *
     * @var array
     *
     */
    protected $tables = array();

    /**
     * 指定したテーブルのデータセットを追加する
     *
     * @param string $tableName
     * @return array カラムとfixtureのデータセット
     */
    public function addTable($tableName)
    {
        $fixtureIns = $this->_createFixtureInstanceByTableName($tableName);

        $columns  = $fixtureIns->getColumns();
        $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);

        $table    = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

        foreach ($fixtureIns->getDatas() as $fixture)
        {
            $table->addRow($fixture);
        }

        $this->tables[$tableName] = $table;

        return array('columns' => $columns, 'fixtures' => $fixtureIns->getDatas());
    }

    /**
     * フィクスチャークラスのインスタンスを生成する
     *
     * @param string $tableName テーブル名
     * @return Setuco_Test_Fixture_Abstract
     * @author suzuki-mar
     */
    private function _createFixtureInstanceByTableName($tableName)
    {
        $fixturePath = $this->_getFixtureBasePath() . $tableName . '.php';

        if (!file_exists($fixturePath)) {
            throw new InvalidArgumentException("{$fixturePath}というフィクスチャーファイルはありません");
        }

        require_once $fixturePath;

        $className  = "Fixture_" . ucfirst($tableName);

        if (!class_exists($className)) {
            throw new InvalidArgumentException("{$className}というフィクスチャークラスはありません");
        }

        $fixtureIns = new $className();

        if (!is_subclass_of($fixtureIns, 'Setuco_Test_Fixture_Abstract')) {
            throw new InvalidArgumentException(
                    "{$className}はフィクスチャークラスではありません Setuco_Test_Fixture_Abstractを継承してください");
        }

        return $fixtureIns;
    }

    /**
     * フィクスチャーのベースパスを取得する
     *
     * @return string フィクスチャーのベースパス
     */
    protected function _getFixtureBasePath()
    {
       return ROOT_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR;
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     * @return PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    protected function createIterator($reverse = FALSE)
    {
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
    }
}


