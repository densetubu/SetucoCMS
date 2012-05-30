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
 * @package    Dev
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
 * @package    Dev
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     suzuki-mar
 */
class Dev_Model_DbInitialization extends Setuco_Model_Abstract
{

    /**
     * 全てのDBを空にする
     *
     * @return boolean 全てからにできたか
     * @author suzuki-mar
     */
    public function truncateAllTables()
    {
        foreach ($this->_getTableNameList() as $name) {
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

        $fixtureHolder  = new Setuco_Fixture_Holder();
        $fixtureInsList = $fixtureHolder->createFixtureInstanceByTableName($this->_getTableNameList());

        foreach ($fixtureInsList as $name => $instance) {
            $className = "Common_Model_DbTable_" . ucfirst($name);

            //テーブルだけあってクラスが存在しない場合は処理しない
            if (class_exists($className)) {
                $dao = new $className($this->getDbAdapter());

                foreach ($instance->getDatas() as $row) {
                    
                    try {
                        $dao->insert($row);
                    } catch(Exception $e) {
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
     * 全てのテーブル名を取得する
     *
     * @return array テーブル名のリスト
     * @author suzuki-mar
     */
    private function _getTableNameList()
    {        
        $names = array();
        foreach($this->_findExecuteResult('show tables') as $rows) {
            $names[] = $rows[0];
        }

        return $names;
    }

    
}

