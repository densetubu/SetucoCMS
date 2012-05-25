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
            $this->_getThumbnailFileName(),
            $this->_getJpegFileName(),
            $this->_getTemplateFileName()
        ); 
    }
    
    private function _getJpegFileName()
    {
        return 'media' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . '1.jpg';
    }

    private function _getThumbnailFileName()
    {
        return 'media' . DIRECTORY_SEPARATOR . 'thumbnail' . DIRECTORY_SEPARATOR . '1.gif';
    }

    private function _getTemplateFileName()
    {
        return 'template' . DIRECTORY_SEPARATOR . '1.html';
    }

}

