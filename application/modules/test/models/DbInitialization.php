<?php

/**
 * DBの初期化をするサービスです。
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
 * @package    Test
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * @category   Setuco
 * @package    Test
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     suzuki-mar
 * @todo       Setuco_Model_Abstractとの親子関係を削除したほうがいいかもしれない その場合はCommonModelにDB全体を管理するクラスを作成する
 * @todo       Testライブラリーに移動したほうがいいかもしれない
 */
class Test_Model_DbInitialization extends Setuco_Model_Abstract
{
    /**
     * @param string[option] $environment 接続するDB環境 通常はdevelopment
     */
    public function  __construct($environment = 'development')
    {
        $adapter = Setuco_Db_ConnectionFactory::create($environment);
        
        parent::__construct($adapter);
    }


    /**
     * 全てのテーブルを削除する
     *
     * @author suzuki-mar
     */
    public function dropAllTables()
    {
        if ($this->emptyDb()) {
            throw new RuntimeException("すでに全てのテーブルは削除されています");
        }


        //外部キーなどの関係で最後に削除する必要があるテーブルリスト
        $laterTableNames = array('account', 'category');
        $firstTableNames = $this->findAllTableNames();

        foreach ($firstTableNames as $key => $name) {
            if (in_array($name, $laterTableNames)) {
                unset($firstTableNames[$key]);
            }
        }

        $this->_dropTables($firstTableNames);
        $this->_dropTables($laterTableNames);
    }

    /**
     * 指定したテーブル名を削除する
     *
     * @param array $names テーブル名
     * @author suzuki-mar
     */
    private function _dropTables(array $tableNames)
    {
        $sql = 'DROP TABLE ';

        foreach ($tableNames as $name) {
            $sql .= "`{$name}`, ";
        }

        $sql = substr($sql, 0, -2);

        $this->getDbAdapter()->exec($sql);
    }


    /**
     * DBがからかどうか
     *
     * @return DBがからかどうか
     * @author suzuki-mar
     */
    public function emptyDb()
    {
        return (!$this->existsTable());
    }

    /**
     * DBにテーブルが存在してるか
     *
     * @return DBにテーブルが存在してるか
     * @author suzuki-mar
     */
     public function existsTable()
     {
        $tableNames = $this->findAllTableNames();
        return (!empty($tableNames)); 
     }


    /**
     * DBを空にして初期化をする
     *
     * @author suzuki-mar
     */
    public function initializeDb()
    {
        if ($this->existsTable()) {
            $this->dropAllTables();
        }


        foreach ($this->_getSchemas() as $sql) {
            $this->getDbAdapter()->exec($sql);
        }
    }

    /**
     * 全てのDBを空にする
     *
     * @return boolean 全てからにできたか
     * @author suzuki-mar
     */
    public function truncateAllTables()
    {
        foreach ($this->findAllTableNames() as $name) {
            $className = "Common_Model_DbTable_" . ucfirst($name);

            //テーブルだけあってクラスが存在しない場合は処理しない
            if (class_exists($className)) {
                $dao = new $className($this->getDbAdapter());
                $dao->delete(true);
            }
        }

        //最後まで処理が通ったので成功とする
        return true;
    }

    /**
     * 全てのフィクスチャーデータをロードできたか
     *
     * @return boolean 全件読み込むことができたか
     * @author suzuki-mar
     */
    public function loadAllFixtureDatas()
    {
        $fixtureHolder = new Setuco_Fixture_Holder();
        $fixtureInsList = $fixtureHolder->createFixtureInstanceByTableName($this->findAllTableNames());

        foreach ($fixtureInsList as $name => $instance) {
            $className = "Common_Model_DbTable_" . ucfirst($name);

            //テーブルだけあってクラスが存在しない場合は処理しない
            if (class_exists($className)) {
                $dao = new $className($this->getDbAdapter());

                foreach ($instance->getDatas() as $row) {

                    try {
                        $dao->insert($row);
                    } catch (Exception $e) {
                        var_dump($name, $e->getMessage());
                        exit;
                    }
                }
            }
        }

        //最後まで処理が通ったので成功とする
        return true;
    }


    /**
     * スキーマファイルをパースしてSQLを返すメソッド
     *
     * @author suzuki-mar
     * @return Array String $querys
     * @todo Installモジュールのサービスと併合する
     */
    private function _getSchemas()
    {
        $comment_flg = false;
        $query = '';

        $fp = fopen(APPLICATION_PATH . '/../sql/initialize_tables.sql', 'r');
        while ($line = fgets($fp)) {
            // MySQLスキーマのファイル内を走査しつつ、コメントは除外して抽出
            if ($comment_flg === true) {

                if (preg_match("/\*\//", $line)) {
                    $comment_flg = false;
                }
            } else {

                if (preg_match("/\/\*/", $line)) {
                    $comment_flg = true;
                } elseif (preg_match("/^\-\-/", $line)) {
                    // コメント行なのでなにもしない
                } else {
                    $query .= $line;
                }
            }
        }
        fclose($fp);

        foreach (explode(";", $query) as $query) {
            $query = trim($query);

            if (empty($query)) {
                continue;
            }

            $querys[] = $query;
        }

        return $querys;
    }
}

