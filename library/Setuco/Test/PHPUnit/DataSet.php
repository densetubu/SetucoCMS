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
     * 指定したテーブルリストのデータセットを追加する
     *
     * requireの関係で配列でいっぺんに渡す
     *
     * @param array $tableNames
     * @return array カラムとfixtureのデータセット
     */
    public function addTables(array $tableNames)
    {
        $fixtureHolder  = new Setuco_Fixture_Holder();
        $fixtureInsList = $fixtureHolder->createFixtureInstanceByTableName($tableNames);

        $fixtureDatas = array();
        foreach ($fixtureInsList as $tableName => $fixtureIns) {
            $columns  = $fixtureIns->getColumns();
            $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);

            $table    = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

            foreach ($fixtureIns->getDatas() as $fixture)
            {
                $table->addRow($fixture);
            }

            $this->tables[$tableName] = $table;
            $fixtureDatas[$tableName] = array('columns' => $columns, 'fixtures' => $fixtureIns->getDatas());
        }

        return $fixtureDatas;
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


