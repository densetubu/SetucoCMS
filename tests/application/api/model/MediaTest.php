<?php

/**
 *
 * @author suzuki-mar
 */
//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

class Api_Model_MediaTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{

    public function setup()
    {
        parent::setup();

        $this->_media = new Api_Model_Media($this->getAdapter());
    }

    protected function _getExceptionAssertColumns()
    {
        return array_merge(parent::_getExceptionAssertColumns(), array('comment'));
    }

    private function _getExpectedOfJpegRow()
    {
       return array(
            'id' => '1',
            'name' => 'image.jpeg',
            'type' => 'jpg',
            'create_date' => '2012-04-11 08:42:09',
            'update_date' => '2012-04-11 08:42:09',
            'comment' => '2012-04-11 08:42:09にアップロード',
            'uploadUrl' => '/media/upload/1.jpg',
            'mediaExists' => true,
            'thumbExists' => true,
            'thumbUrl' => '/media/thumbnail/1.gif',
            'thumbWidth' => Setuco_Data_Constant_Media::THUMB_WIDTH,
        );
    }

    private function _getExpectedOfJpeg()
    {
        $data = $this->_getExpectedOfJpegRow();
        $data['uploadUrl'] = 'http://setucocms.localdomain/media/upload/1.jpg';
        $data['thumbUrl'] = 'http://setucocms.localdomain/media/thumbnail/1.gif';

        return $data;
    }

    private function _getExpectedOfPngRow()
    {
        return array(
            'id' => '2',
            'name' => 'image.png',
            'type' => 'png',
            'create_date' => '2012-04-11 08:42:09',
            'update_date' => '2012-04-11 08:42:09',
            'comment' => '2012-04-11 08:42:09にアップロード',
            'uploadUrl' => '/media/upload/2.png',
            'mediaExists' => true,
            'thumbExists' => true,
            'thumbUrl' => '/media/thumbnail/2.gif',
            'thumbWidth' => Setuco_Data_Constant_Media::THUMB_WIDTH,
        );
    }


    private function _getExpectedOfPng()
    {
        $data = $this->_getExpectedOfPngRow();
        $data['uploadUrl'] = 'http://setucocms.localdomain/media/upload/2.png';
        $data['thumbUrl'] = 'http://setucocms.localdomain/media/thumbnail/2.gif';

        return $data;
    }

    private function _getExpectedOfPdf()
    {
        return array(
            'id' => '3',
            'name' => 'sample.pdf',
            'type' => 'pdf',
            'create_date' => '2012-04-11 08:42:09',
            'update_date' => '2012-04-11 08:42:09',
            'comment' => '2012-04-11 08:42:09にアップロード',
            'uploadUrl' => 'http://setucocms.localdomain/media/upload/3.pdf',
            'mediaExists' => true,
            'thumbExists' => true,
            'thumbUrl' => 'http://setucocms.localdomain/images/admin/media/icn_pdf.gif',
            'thumbWidth' => Setuco_Data_Constant_Media::THUMB_WIDTH,
        );
    }

    public function test_findAllMediaInfos()
    {
        $expected = array(
            $this->_getExpectedOfJpeg(),
            $this->_getExpectedOfPng(),
            $this->_getExpectedOfPdf(),
        );

        $this->assertRowDatas($expected, $this->_media->findAllMediaInfos());
    }

    public function test_findImageMediaInfos()
    {
        $expected = array(
            $this->_getExpectedOfJpeg(),
            $this->_getExpectedOfPng(),
        );

        $this->assertRowDatas($expected, $this->_media->findImageMediaInfos());
    }

    public function test_findEtcMediasInfos_画像以外のデータを取得する()
    {
        $expected = array(
            $this->_getExpectedOfPdf(),
        );

        $this->assertRowDatas($expected, $this->_media->findEtcMediaInfos());
    }

    public function test_findImageMedias_画像のデータを取得する()
    {
        $expected = array(
            $this->_getExpectedOfJpegRow(),
            $this->_getExpectedOfPngRow(),
        );

        $this->assertRowDatas($expected, $this->_media->findImageMedias());
    }

}
