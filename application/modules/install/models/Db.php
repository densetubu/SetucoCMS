<?php

/**
 * データベースに関するクラス
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
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @since      File available since Release 1.6.1
 * @author     Takayuki Otake
 */

/**
 * SetucoCMSのインストール時に利用するモデルクラス
 *
 * @author Takayuki Otake
 */
class Install_Model_Db
{

    /**
     * データベースハンドラ
     */
    private $dbh;

    /**
     * コンストラクタ
     *
     * @param $params array String
     * @author Takayuki Otake
     */
    public function __construct($params = array())
    {
        var_dump($params);
        try {
            $this->dbh = new PDO(
                "mysql:host={$params['db_host']}; dbname={$params['db_name']}",
                $params['db_user'],
                $params['db_pass']
            );
            $this->dbh->query("SET NAMES utf8");
        } catch (PDOException $e) {
            //TODO: ちゃんとしたものにしょう
            throw new Setuco_Exception(
                'データベースの接続に失敗しました。', $e->getMessage()
            );
        }
    }

    /**
     * デストラクタ
     *
     * @param $params array String
     * @author Takayuki Otake
     */
    public function __destruct()
    {
        $this->dbh = null;
    }

    /**
     * スキーマのセットアップをするメソッド
     *
     * @author Takayuki Otake
     */
    public function setupSchema()
    {
        try {
            $querys = $this->_getSchemas();
            foreach ($querys as $query){
                if (!empty($query)){
                    $this->dbh->query($query);
                }
            }
        } catch (PDOException $e) {
            //TODO: ちゃんとしたものにしよう
            throw new Setuco_Exception(
                'エラーが発生しました。', $e->getMessage()
            );
        }

    }

    /**
     * アカウント情報のアップデートをするメソッド
     *
     * @param $params array String
     * @author Takayuki Otake
     */
    public function updateAccount($params = array())
    {
        try {
            $sth = $this->dbh->prepare('
                UPDATE account set login_id = ?, password = SHA1(?)
                ');
            $sth->execute(array(
                $params['account_id'], 
                $params['account_pass']
                ));
        } catch (Exception $e) {
            throw new Setuco_Exception(
                'エラーが発生しました。', $e->getMessage()
            );
        }
    }

    /**
     * サイト情報のアップデートをするメソッド
     *
     * @param $params array String
     * @author Takayuki Otake
     */
    public function updateSite($params = array())
    {
        if (empty($params)) {
            return false;
        }

        $sth = $this->dbh->prepare('
            UPDATE site SET name = ?, url = ?, comment = ? WHERE id = ?
            ');
        return $sth->execute(array(
            $params['site_name'], 
            $params['site_url'], 
            $params['site_comment'],
            $params['site_id']
        ));
    }

    /**
     * データベースに接続するメソッド
     *
     * @author Takayuki Otake
     * @return boolean
     */
    private function connect($params = array())
    {
        try {
            $this->dbh = new PDO(
                "mysql:host={$params['db_host']}; dbname={$params['db_name']}",
                $params['db_user'],
                $params['db_pass']
            );
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * スキーマファイルをパースしてSQLを返すメソッド
     *
     * @author Takayuki Otake
     * @return Array String $querys
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
                    $query .= trim($line);
                }
            }
        }
        fclose($fp);

        foreach (explode(";", $query) as $query) {
            if (empty($query)) {
                continue;
            }

            $querys[] = $query;
        }

        return $querys;
    }

}
