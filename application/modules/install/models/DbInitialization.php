<?php

/**
 * DBの初期化をするサービスです。
 * copy from Test_Model_DbInitialization
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
 * @package    Install
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 1.5.0
 * @author     suzuki-mar, Takayuki Otake
 */

/**
 * @category   Setuco
 * @package    Install
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     suzuki-mar
 * @todo       Setuco_Model_Abstractとの親子関係を削除したほうがいいかもしれない その場合はCommonModelにDB全体を管理するクラスを作成する
 * @todo       Testライブラリーに移動したほうがいいかもしれない
 */
class Install_Model_DbInitialization extends Setuco_Model_Abstract
{
    /**
     * @param string[option] $environment 接続するDB環境
     */
    public function  __construct($environment = 'production')
    {
        $adapter = Setuco_Db_ConnectionFactory::create($environment);
        
        parent::__construct($adapter);
    }



    /**
     * アカウント情報を更新する
     *
     * @author Takayuki Otake
     * @todo バリデーションの強化
     * @todo where句指定の追加
     */
    public function updateAccount($params = array())
    {
        if (empty($params)) {
            throw new RuntimeException('引数が空です');
        }

        if (!isset($params['login_id']) || empty($params['login_id'])) {
            throw new RuntimeException('ログインIDが空です');
        }
        if (!isset($params['password']) || empty($params['password'])) {
            throw new RuntimeException('パスワードが空です');
        }

        $updateValues = array(
            'login_id' => $params['login_id'],
            'password' => sha1($params['password']),
        );

        $this->getDbAdapter()->update('account', $updateValues);
    }


    /**
     * サイト情報を更新する
     *
     * @author Takayuki Otake
     * @todo バリデーションの強化
     * @todo where句指定の追加
     */
    public function updateSite($params = array())
    {
        if (empty($params)) {
            throw new RuntimeException('引数が空です');
        }

        if (!isset($params['name']) || empty($params['name'])) {
            throw new RuntimeException('サイト名が空です');
        }
        if (!isset($params['url']) || empty($params['url'])) {
            throw new RuntimeException('サイトURLが空です');
        }
        if (!isset($params['comment']) || empty($params['comment'])) {
            throw new RuntimeException('サイトの説明が空です');
        }

        $updateValues = array(
            'name'    => $params['name'],
            'url'     => $params['url'],
            'comment' => $params['comment'],
        );

        $this->getDbAdapter()->update('site', $updateValues);
    }


    /**
     * DBを初期化をする
     *
     * @author suzuki-mar
     */
    public function initializeDb()
    {
        foreach ($this->_getSchemas() as $sql) {
            $this->getDbAdapter()->exec($sql);
        }
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
            if ($comment_flg) {

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

