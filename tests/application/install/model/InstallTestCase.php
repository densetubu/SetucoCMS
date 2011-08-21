<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InstallTestCase
 *
 * @author suzukimasayuki
 */
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

require_once "/Users/suzukimasayuki/project/setucodev/application/modules/install/models/Install.php";

class InstallTestCase extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Install_Install
     */
    private $_installService;

    const DB_INFO_HOST = 'localhost';
    const DB_INFO_USER = 'setuco';
    const DB_INFO_USER_NO_CREATE = 'access_user';
    const DB_INFO_PASSWORD = 'setuco';
    const DB_INFO_DB = 'setucocms';

    public function setup()
    {
        $this->_installService = new Install_InstallTest();
    }

    public function test_checkDbPermission()
    {
        $params = array(
            'host' => self::DB_INFO_HOST,
            'user' => self::DB_INFO_USER,
            'password' => self::DB_INFO_PASSWORD,
            'db' => self::DB_INFO_DB
        );

        $this->assertTrue($this->_installService->checkDbPermission($params));
    }

    public function test_checkDbPermission_接続情報が違っていたらfalse_host名が違う()
    {
        $params = array(
            'host' => 'false',
            'user' => self::DB_INFO_USER,
            'password' => self::DB_INFO_PASSWORD,
            'db' => self::DB_INFO_DB
        );

        $this->assertFalse($this->_installService->checkDbPermission($params));
    }

    public function test_checkDbPermission_接続情報が違っていたらfalse_ユーザー名が違う()
    {
        $params = array(
            'host' => self::DB_INFO_HOST,
            'user' => '',
            'password' => self::DB_INFO_PASSWORD,
            'db' => self::DB_INFO_DB
        );

        $this->assertFalse($this->_installService->checkDbPermission($params));
    }

    public function test_checkDbPermission_テーブル作成権限がない状態はfalse()
    {
        $params = array(
            'host' => self::DB_INFO_HOST,
            'user' => self::DB_INFO_USER_NO_CREATE,
            'password' => self::DB_INFO_PASSWORD,
            'db' => self::DB_INFO_DB
        );

        $this->assertFalse($this->_installService->checkDbPermission($params));
    }

    public function test_getInitializeTablesSql_テーブルを初期化する()
    {
        $this->assertEquals($this->_installService->_getInitializeTablesSql(), $this->_getInitializeQuerys());
    }

    

    private function _getInitializeQuerys()
    {
        return array(
            0 => 'CREATE TABLE account (id       INT AUTO_INCREMENT NOT NULL,login_id VARCHAR(255) NOT NULL,nickname VARCHAR(255) NOT NULL,password TEXT NOT NULL,PRIMARY KEY(id),UNIQUE(login_id),UNIQUE(nickname)) TYPE=INNODB CHARACTER SET utf8 COLLATE utf8_bin',
            1 => 'CREATE TABLE tag (id      INT AUTO_INCREMENT NOT NULL,name    VARCHAR(255) NOT NULL,PRIMARY KEY(id),UNIQUE(name)) TYPE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci',
            2 => 'CREATE TABLE media (id          INT AUTO_INCREMENT NOT NULL,name        TEXT NOT NULL,type        TEXT NOT NULL,create_date TIMESTAMP NOT NULL DEFAULT \'0000-00-00 00:00:00\',update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,comment     TEXT,PRIMARY KEY(id)) TYPE=INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci',
            3 => 'CREATE TABLE category (id          INT AUTO_INCREMENT NOT NULL,name        VARCHAR(255) NOT NULL,parent_id   INT DEFAULT -1,PRIMARY KEY(id),UNIQUE(name, parent_id),FOREIGN KEY(parent_id) REFERENCES category(id) ON DELETE CASCADE) TYPE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci',
            4 => 'CREATE TABLE page (id          INT AUTO_INCREMENT NOT NULL,title       TEXT NOT NULL,contents    TEXT NOT NULL,outline     TEXT,status      INT NOT NULL,category_id INT,account_id  INT,create_date TIMESTAMP NOT NULL DEFAULT \'0000-00-00 00:00:00\',update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY(id),FOREIGN KEY(account_id) REFERENCES account(id) ON DELETE SET NULL,FOREIGN KEY(category_id) REFERENCES category(id) ON DELETE SET NULL) TYPE=INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci',
            5 => 'CREATE TABLE page_media (page_id INT NOT NULL,media_id INT NOT NULL,PRIMARY KEY(media_id,page_id),FOREIGN KEY(page_id) REFERENCES page(id) ON DELETE CASCADE,FOREIGN KEY(media_id) REFERENCES media(id) ON DELETE CASCADE) CHARACTER SET utf8 COLLATE utf8_bin',
            6 => 'CREATE TABLE page_tag (page_id     INT NOT NULL,tag_id      INT NOT NULL,PRIMARY KEY(page_id,tag_id),FOREIGN KEY(page_id) REFERENCES page(id) ON DELETE CASCADE,FOREIGN KEY(tag_id) REFERENCES tag(id) ON DELETE CASCADE) CHARACTER SET utf8 COLLATE utf8_bin',
            7 => 'CREATE TABLE site (id          INT AUTO_INCREMENT NOT NULL,name        TEXT NOT NULL,url         TEXT NOT NULL,comment     TEXT,keyword     TEXT,open_date   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(id)) CHARACTER SET utf8 COLLATE utf8_unicode_ci',
            8 => 'CREATE TABLE ambition (id          INT AUTO_INCREMENT NOT NULL,ambition    TEXT NOT NULL,PRIMARY KEY(id)) CHARACTER SET utf8 COLLATE utf8_unicode_ci',
            9 => 'CREATE TABLE goal (id           INT AUTO_INCREMENT NOT NULL,page_count   INT NOT NULL,target_month DATE NOT NULL UNIQUE,PRIMARY KEY(id)) CHARACTER SET utf8 COLLATE utf8_bin',
            10 => 'CREATE INDEX page_tag_tag_id_indexON page_tag(tag_id)',
            11 => 'INSERT INTO account(login_id, nickname, password)VALUES(\'admin\', \'管理者\', SHA1(\'password\'))',
            12 => 'INSERT INTO category(id, name, parent_id)VALUES(-1, \'no_parent\', null)',
            13 => 'INSERT INTO site(name, url, comment, keyword, open_date)VALUES(\'サイト名を設定してください\',\'http://example.com/\',\'サイトの説明を設定してください。\',\'サイトのキーワードを設定してください。\',now())',
            14 => 'INSERT INTO ambition(ambition)VALUES(\'目標を設定してください。\')',
            15 => 'INSERT INTO goal(page_count, target_month)VALUES(10, cast(date_format(now(), \'%Y-%m-1\') as date))',
            16 => 'commit',
        );
    }

}

class Install_InstallTest extends Install_Install
{

    public function _getInitializeTablesSql()
    {
        return parent::_getInitializeTablesSql();
    }

}

