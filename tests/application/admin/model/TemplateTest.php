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
 
    public function setup()
    {
        parent::setup();

        $this->_template = new Admin_Model_TemplateMock($this->getAdapter());
    }

    public function test_registTemplate_テンプレートデータを登録する()
    {
        $createdFile = $this->_getCreateFileDirPath() . '3.html';

        if (file_exists($createdFile)) {
            unlink($createdFile);
        }

        $params['account_id']   = Fixture_Account::ADMIN_ID;
        $params['title']        = '管理者のテンプレート';
        $params['explanation']  = '管理者専用のテンプレート';
        $params['content']      = '管理しましょう';

        $this->assertTrue($this->_template->registTemplate($params));
        $this->assertFileExists($createdFile);

        $expectedFile = $this->_getCreateFileDirPath() . 'first_expected.html';
        $this->assertFileEquals($expectedFile, $createdFile);

        $createdFile = $this->_getCreateFileDirPath() . '4.html';

        if (file_exists($createdFile)) {
            unlink($createdFile);
        }

        $params['account_id']   = Fixture_Account::GENERAL_ID;
        $params['title']        = 'ユーザーのテンプレート';
        $params['explanation']  = '全員使用できるテンプレート';
        $params['content']      = 'ページを書きましょう';

        $this->assertTrue($this->_template->registTemplate($params));
        $this->assertFileExists($createdFile);

        $expectedFile = $this->_getCreateFileDirPath() . 'second_expected.html';
        $this->assertFileEquals($expectedFile, $createdFile);

    }

    private function _getCreateFileDirPath()
    {
       return TEST_DIR . '/data/template/';
    }

    public function test_findNextFileName_次の保存するファイル名を取得する()
    {
        $this->assertSame("3", $this->_template->findNextFileName());
    }
    


}

class Admin_Model_TemplateMock extends Admin_Model_Template
{
    /**
     * テンプレートを保存するベースとなるパスを取得する
     *
     * @return string テンプレートのベースパス
     */
    protected function _getBasePath()
    {
        return TEST_DIR . '/data/template/';
    }
}
