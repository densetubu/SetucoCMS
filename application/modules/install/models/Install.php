<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Install
 *
 * @author suzukimasayuki
 */
class Install_Install
{

    public function checkDbPermission(array $params)
    {
        try {
            $dbh = new PDO("mysql:host={$params['host']}; dbname={$params['db']}", $params['user'], $params['password']);
        } catch (PDOException $e) {
            return false;
        }

        $result = $dbh->query('CREATE TABLE setuco_install_test (id int, value text)');

        if ($result === false) {
            return false;
        }

        $dbh->query('DROP TABLE setuco_install_test');

        return true;
    }

    protected function _getInitializeTablesSql()
    {

        $comment_flg = false;

        $fp = fopen(APPLICATION_PATH . '/../sql/initialize_tables.sql', 'r');

        $query = '';
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
                    // なにもしない
                } else {
                    $query .= trim($line);
                }
            }
        }
        fclose($fp);

        // コメントを除いたスキーマを1行づつのクエリーに分割して配列に格納
        $querys = explode(";", $query);

        array_pop($querys);

        return $querys;
    }

}
