<?php

/**
 * @todo ダウンロードしてきたファイルを圧縮できるようにする
 * @todo 取得してきたファイルを置換することができない
 */
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 *
 * @author suzuki-mar
 */
class CodeUpgradeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Setuco_Migration_CodeUpgrade
     */
    private $_upgrader;

    const TEST_DOWNLOAD_URL = 'https://github.com/suzuki-mar/DownloadTest/zipball/master';

    public static function TEST_DATA_PATH()
    {
        return Setuco_Data_Constant_DirPath::TEST_PATH() . '/data/downloads/';
    }

    public static function TEST_EXTRACT_PATH()
    {
        return Setuco_Data_Constant_DirPath::TEST_PATH() . '/data/downloads/extract/';
    }

    public static function TEST_TARGET_PATH()
    {
        return self::TEST_DATA_PATH() . 'searchTarget/';
    }

    public function setUp()
    {
        parent::setUp();

        $params = array(
            'save_path' => self::TEST_DATA_PATH(),
            'download_url' => self::TEST_DOWNLOAD_URL,
            'extract_path' => self::TEST_EXTRACT_PATH(),
            'target_path' => self::TEST_TARGET_PATH()
        );

        $this->_upgrader = new Setuco_Migration_CodeUpgrade($params);
    }

    /**
     * @test
     * @group first
     */
    public function construct_ダウンロードするパスの指定がなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $params = array(
            'url' => 'test',
            'extract_path' => 'extract_path',
            'target_path' => self::TEST_TARGET_PATH()
        );

        new Setuco_Migration_CodeUpgrade($params);
    }

    /**
     * @test
     * @group first
     */
    public function construct_URLの指定がなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $params = array(
            'save_path' => 'test',
            'extract_path' => 'extract_path',
            'target_path' => self::TEST_TARGET_PATH()
        );

        new Setuco_Migration_CodeUpgrade($params);
    }

    /**
     * @test
     * @group 
     */
    public function construct_解凍するディレクトリの指定がなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $params = array(
            'save_path' => 'test',
            'download_url' => 'download',
            'target_path' => self::TEST_TARGET_PATH()
        );

        new Setuco_Migration_CodeUpgrade($params);
    }

    /**
     * @test
     * @group 
     */
    public function construct_アップグレードするディレクトリの指定がなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $params = array(
            'save_path' => 'test',
            'download_url' => 'download',
            'extract_path' => 'extract_path',
        );

        new Setuco_Migration_CodeUpgrade($params);
    }

    /**
     * @test
     * @group 
     */
    public function checkConfig_ダウンロードしたのを保存するディレクトリに書き込み権限がなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $params = array(
            'save_path' => self::TEST_DATA_PATH() . 'not_writed/',
            'download_url' => self::TEST_DOWNLOAD_URL,
            'extract_path' => self::TEST_EXTRACT_PATH(),
            'target_path' => self::TEST_TARGET_PATH()
        );

        $this->_upgrader = new Setuco_Migration_CodeUpgrade($params);

        $this->_upgrader->checkConfig();
    }

    /**
     * @test
     * @group 
     */
    public function checkConfig_ダウンロード用のURLが存在しないURLだったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $params = array(
            'save_path' => self::TEST_DATA_PATH(),
            'download_url' => "https://github.com/suzuki-mar/file_not_exists/zipball/master",
            'extract_path' => self::TEST_EXTRACT_PATH(),
            'target_path' => self::TEST_TARGET_PATH()
        );

        $this->_upgrader = new Setuco_Migration_CodeUpgrade($params);

        $this->_upgrader->checkConfig();
    }

    /**
     * @test
     * @group first
     */
    public function searchFilePathsByBasePath_アップグレード先のディレクトリが存在しなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $params = array(
            'save_path' => self::TEST_DATA_PATH(),
            'download_url' => self::TEST_DOWNLOAD_URL,
            'extract_path' => self::TEST_EXTRACT_PATH(),
            'target_path' => 'not_exists'
        );

        $this->_upgrader = new Setuco_Migration_CodeUpgrade($params);

        $this->_upgrader->checkConfig();
    }

    /**
     * @test
     * @group first
     */
    public function download_ダウンロードしてくるディレクトリに書き込み権限がなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $params = array(
            'save_path' => Setuco_Data_Constant_DirPath::TEST_PATH() . "/data/downloads/not_writed",
            'download_url' => self::TEST_DOWNLOAD_URL,
            'extract_path' => self::TEST_EXTRACT_PATH()
        );


        $upgrader = new Setuco_Migration_CodeUpgrade($params);
        $upgrader->checkConfig();
    }

    /**
     * @test
     */
    public function download_指定したリポジトリのコードを指定したURLからダウンロードすることができる()
    {

        $savePath = 'test';

        if (file_exists($savePath)) {
            unlink($savePath);
        }

        $url = "https://github.com/suzuki-mar/DownloadTest/zipball/master";

        $this->assertTrue(
                $this->_upgrader->download('test')
        );

        $savePath = self::TEST_DATA_PATH() . 'test';

        $this->assertFileCompare($savePath);
    }

    /**
     * @test
     * @group first
     */
    public function searchFilePaths_指定したディレクトリパスに存在するすべてのファイルを取得する()
    {
        $expected = array(
            self::TEST_TARGET_PATH() . 'sample.php',
            self::TEST_TARGET_PATH() . 'subDir/subfile.php',
            self::TEST_TARGET_PATH() . 'subDir/subfile2.php',
            self::TEST_TARGET_PATH() . 'subDir/subsubDir/subsubFile',
        );

        $this->assertEquals(
                $expected,
                $this->_upgrader->searchFilePaths()
        );
    }

    /**
     * @test
     * @group first
     */
    public function searchFilePaths_スラッシュがついていなくても正常に動作する()
    {
        $expected = array(
            self::TEST_TARGET_PATH() . 'sample.php',
            self::TEST_TARGET_PATH() . 'subDir/subfile.php',
            self::TEST_TARGET_PATH() . 'subDir/subfile2.php',
            self::TEST_TARGET_PATH() . 'subDir/subsubDir/subsubFile',
        );

        $this->assertEquals(
                $expected,
                $this->_upgrader->searchFilePaths()
        );
    }

    /**
     * @test
     * @group first
     */
    public function searchSecretFilePathsByPath_指定したディレクトリの隠しパスを取得する()
    {
        $expected = array(
            self::TEST_TARGET_PATH() . '/.secret',
        );

        $this->assertEquals(
                $expected,
                $this->_upgrader->searchSecretFilePathsByPath(self::TEST_TARGET_PATH())
        );
    }

    /**
     * @test
     * @group first
     */
    public function searchSecretFilePathsByPath_指定したディレクトリにファイルがなかったら空の配列を返す()
    {
        $dirPath = self::TEST_TARGET_PATH() . 'subDir';

        $expected = array();

        $this->assertEquals(
                $expected,
                $this->_upgrader->searchSecretFilePathsByPath($dirPath)
        );
    }

    /**
     * @test
     * @group first
     */
    public function searchSecretFilePathsByPath_検索するディレクトリが存在しなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->_upgrader->searchSecretFilePathsByPath('not_exists');
    }

    /**
     * @test
     * @group first
     */
    public function searchAllFilePaths_隠しファイルを含めてすべてのファイルパスを取得する()
    {
        $expected = array(
            self::TEST_TARGET_PATH() . 'sample.php',
            self::TEST_TARGET_PATH() . 'subDir/subfile.php',
            self::TEST_TARGET_PATH() . 'subDir/subfile2.php',
            self::TEST_TARGET_PATH() . 'subDir/subsubDir/subsubFile',
            self::TEST_TARGET_PATH() . '.secret',
        );

        $this->assertEquals(
                $expected,
                $this->_upgrader->searchAllFilePaths()
        );
    }

    /**
     * @test
     * @group first
     */
    public function checkWritePermissionFileList_指定したファイルリストに書き込み権限がない場合は例外を発生させる()
    {
        $basePath = self::TEST_DATA_PATH() . 'not_write_file/';

        $errorPath = $basePath . 'not_writed.php';
        $this->setExpectedException('InvalidArgumentException', "{$errorPath}には書き込み権限がありません");

        $fileList = array(
            $basePath . 'not_writed.php',
            $basePath . 'writed.php',
        );

        $this->_upgrader->checkWritePermissionFileList($fileList);
    }

    /**
     * @test
     * @group first
     */
    public function checkWritePermissionFileList_すべてのファイルに書き込み権限があるとtrue()
    {
        $basePath = self::TEST_DATA_PATH() . 'not_write_file/';

        $fileList = array(
            $basePath . 'writed.php',
        );

        $this->assertTrue(
                $this->_upgrader->checkWritePermissionFileList($fileList)
        );
    }

    /**
     * @test
     * @group first
     */
    public function isOverWriteFile_ファイルの容量が違っていたらtrue()
    {
        $targetFile = self::TEST_TARGET_PATH() . 'subDir/subfile.php';
        $compareFile = self::TEST_TARGET_PATH() . 'sample.php';

        $this->assertTrue(
                $this->_upgrader->isOverWriteFile($targetFile, $compareFile)
        );
    }

    /**
     * @test
     * @group first
     */
    public function isOverWriteFile_ファイルの容量が違っていたらfalse()
    {
        $targetFile = self::TEST_TARGET_PATH() . 'subDir/subfile.php';
        $compareFile = self::TEST_TARGET_PATH() . 'subDir/subfile2.php';

        $this->assertFalse(
                $this->_upgrader->isOverWriteFile($targetFile, $compareFile)
        );
    }

    /**
     * @test
     * @group first
     */
    public function isOverWriteFile_置換するファイルがなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $targetFile = 'not_exists';
        $compareFile = self::TEST_TARGET_PATH() . 'subDir/subfile2.php';

        $this->assertFalse(
                $this->_upgrader->isOverWriteFile($targetFile, $compareFile)
        );
    }

    /**
     * @test
     * @group first
     */
    public function isOverWriteFile_置換先のファイルがなかったら例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException');

        $targetFile = self::TEST_TARGET_PATH() . 'subDir/subfile.php';
        $compareFile = 'not_exists';

        $this->assertFalse(
                $this->_upgrader->isOverWriteFile($targetFile, $compareFile)
        );
    }

    /**
     * @test
     * @group first
     */
    public function extractZipFile_Zipファイルを指定した場所に解凍する()
    {

    }

    /**
     *
     * @param <type> $expected
     */
    protected function assertFileCompare($expected)
    {
        $compareFile = $expected . '_compare';

        if (file_exists($compareFile)) {
            $this->assertFileEquals($expected, $compareFile);
        } else {
            $this->assertFileExists($expected);
            copy($expected, $compareFile);
        }
    }

}