<?php

/**
 *
 * @author suzuki-mar
 */

//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}


class Admin_Model_TemplateTest extends Setuco_Test_PHPUnit_TestCase
{
    const CREATE_FILE_DIR = '/Users/suzukimasayuki/project/setucodev/tests/data/template/';


    public function setup()
    {
        parent::setup();
        $this->_template = new Admin_Model_Template();
    }

    public function test_create_テンプレートファイルを作成する()
    {
        $this->assertTrue($this->_template->create('test'));
        $createdFile = self::CREATE_FILE_DIR . 'test.html';
        $this->assertFileExists($createdFile);

        $this->assertTrue($this->_template->create('second'));
        $createdFile = self::CREATE_FILE_DIR . 'second.html';
        $this->assertFileExists($createdFile);

    }

}



