<?php

/**
 *
 * @author suzuki-mar
 */

//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}


class Admin_Model_TemplateTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{
    const CREATE_FILE_DIR = '/Users/suzukimasayuki/project/setucodev/tests/data/template/';


    public function setup()
    {
        parent::setup();

        $this->_template = new Admin_Model_Template($this->getAdapter());
    }

    public function test_registTemplate_テンプレートデータを登録する()
    {
        $createdFile = self::CREATE_FILE_DIR . '3.html';

        if (file_exists($createdFile)) {
            unlink($createdFile);
        }

        $params['account_id']   = Fixture_Account::ADMIN_ID;
        $params['title']        = '管理者のテンプレート';
        $params['explanation']  = '管理者専用のテンプレート';
        $params['content']      = '管理しましょう';

        $this->assertTrue($this->_template->registTemplate($params));
        $this->assertFileExists($createdFile);

        $expectedFile = self::CREATE_FILE_DIR . 'first_expected.html';
        $this->assertFileEquals($expectedFile, $createdFile);

        $createdFile = self::CREATE_FILE_DIR . '4.html';

        if (file_exists($createdFile)) {
            unlink($createdFile);
        }

        $params['account_id']   = Fixture_Account::GENERAL_ID;
        $params['title']        = 'ユーザーのテンプレート';
        $params['explanation']  = '全員使用できるテンプレート';
        $params['content']      = 'ページを書きましょう';

        $this->assertTrue($this->_template->registTemplate($params));
        $this->assertFileExists($createdFile);

        $expectedFile = self::CREATE_FILE_DIR . 'second_expected.html';
        $this->assertFileEquals($expectedFile, $createdFile);

    }

    public function test_findNextFileName_次の保存するファイル名を取得する()
    {
        $this->assertSame("3", $this->_template->findNextFileName());
    }
    


}



