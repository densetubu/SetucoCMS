<?php

//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

class Dev_DbTable_FileInitializationTest extends Setuco_Test_PHPUnit_TestCase
{

    public function setup()
    {
        parent::setup();
        $this->_service = new Dev_Model_FileInitialization();
    }

    public function test_getFilePathList_ファイルパスを取得する()
    {
        $this->assertEquals($this->_getFileNames(), $this->_service->getFilePathList());
    }

    public function test_copyFixtureFile_ファイルをコピーする()
    {

        foreach ($this->_getFileNames() as $fileName) {
            $targetPath = ROOT_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->_getJpegFileName();

            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
        }


        $this->_service->copyFixtureFile();

        foreach ($this->_getFileNames() as $fileName) {
            $targetPath = ROOT_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->_getJpegFileName();
            $this->assertFileExists($targetPath);
        }
    }

    public function test_deleteFixtureFile_フィクスチャーのファイルを削除する()
    {

        //アップロードしているファイルを削除する必要があるので一回ファイルを作成する
        $this->_service->copyFixtureFile();

        $this->_service->deleteUploadFile();

        foreach ($this->_getFileNames() as $fileName) {
            $targetPath = ROOT_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->_getJpegFileName();
            $this->assertFileNotExists($targetPath);
        }
    }

    private function _getFileNames()
    {
        return array(
            $this->_getThumbnailJpegFileName(),
            $this->_getThumbnailPngFileName(),
            $this->_getJpegFileName(),
            $this->_getPngFileName(),
            $this->_getPdfFileName(),
            $this->_getTemplateAdminFileName(),
            $this->_getTemplateUserFileName(),
        );
    }

    private function _getBaseFilePath()
    {
        return substr(Setuco_Data_Constant_Media::UPLOAD_DIR_PATH_FROM_BASE, 1);
    }

    private function _getBaseThumbnailPath()
    {
        return substr(Setuco_Data_Constant_Media::THUMB_DIR_PATH_FROM_BASE, 1);
    }


    private function _getJpegFileName()
    {
        return $this->_getBaseFilePath() . Fixture_Media::ID_JPEG . '.jpg';
    }

    private function _getThumbnailJpegFileName()
    {
        return $this->_getBaseThumbnailPath() . Fixture_Media::ID_JPEG . '.gif';
    }

    private function _getPngFileName()
    {
        return $this->_getBaseFilePath() .  Fixture_Media::ID_PNG . '.png';
    }

    private function _getThumbnailPngFileName()
    {
        return $this->_getBaseThumbnailPath() .  Fixture_Media::ID_PNG . '.gif';
    }

    private function _getPdfFileName()
    {
        return $this->_getBaseFilePath() . Fixture_Media::ID_PDF . '.pdf';
    }

    private function _getTemplateAdminFileName()
    {
        return Setuco_Data_Constant_Template::BASE_NAME . Fixture_Template::TOP_ID . '.html';
    }

    private function _getTemplateUserFileName()
    {
        return Setuco_Data_Constant_Template::BASE_NAME . Fixture_Template::USER_CREATE_ID . '.html';
    }

}

