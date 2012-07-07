<?php

/**
 * Setucoのモデルの最基底クラス
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
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * @package    Setuco
 * @subpackage Model
 * @author     suzuki-mar
 */
class Setuco_Model_Abstract
{
    /**
     * DAOクラスを生成するときに必要になる
     *
     * @var Zend_Db_Adapter_Pdo_Abstract
     */
    private $_adapter;

    public function  __construct(Zend_Db_Adapter_Pdo_Abstract $adapter = null)
    {
        if ($adapter === null) {
            $this->_adapter = Setuco_Db_ConnectionFactory::create();
        } else {
            $this->_adapter = $adapter;
        }
    }

    /**
     * DBのアダプターを取得する
     *
     * @return Zend_Db_Adapter_Pdo_Abstract
     */
    public function getDbAdapter()
    {
        return $this->_adapter;
    }

    /**
     * 実行したSQLの結果を取得する
     *
     * @param  String $sql 実行するSQL
     * @param  string[option] $pdoMode デフォルトは PDO::FETCH_BOTH
     * @return array 指定したPDOのモード
     * @author suzuki-mar
     */
    protected function _findExecuteResult($sql, $pdoMode = PDO::FETCH_BOTH)
    {
        $connection = $this->getDbAdapter()->getConnection();
        $statement = $connection->query($sql);
        return $statement->fetchAll($pdoMode);
    }

    /**
     * すべてのテーブル名を取得する
     *
     * @return array テーブル名の一覧
     * @author suzuki-mar
     */
    public function findAllTableNames()
    {
        $searchResult = $this->_findExecuteResult('show tables', PDO::FETCH_NUM);

        $names = array();
        foreach($searchResult as $row) {
            foreach($row as $value) {
                $names[] = trim($value);
            }
        }

        return $names;
    }

}