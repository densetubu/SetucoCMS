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

    private function _getExpectedOfJpeg()
    {
        return array(
            'id' => '1',
            'name' => 'image.jpeg',
            'type' => 'jpg',
            'create_date' => '2012-04-11 08:42:09',
            'update_date' => '2012-04-11 08:42:09',
            'comment' => '2012-04-11 08:42:09にアップロード',
            'uploadUrl' => 'http://setucocms.localdomain/media/upload/1.jpg',
            'mediaExists' => true,
            'thumbExists' => false,
            'thumbUrl' => 'http://setucocms.localdomain/media/thumbnail/1.gif',
            'thumbWidth' => 0,
        );
    }

    private function _getExpectedOfPng()
    {
        return array(
            'id' => '2',
            'name' => 'image.png',
            'type' => 'png',
            'create_date' => '2012-04-11 08:42:09',
            'update_date' => '2012-04-11 08:42:09',
            'comment' => '2012-04-11 08:42:09にアップロード',
            'uploadUrl' => 'http://setucocms.localdomain/media/upload/2.png',
            'mediaExists' => false,
            'thumbExists' => false,
            'thumbUrl' => 'http://setucocms.localdomain/media/thumbnail/2.gif',
            'thumbWidth' => 0,
        );
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
            'mediaExists' => false,
            'thumbExists' => true,
            'thumbUrl' => 'http://setucocms.localdomain/images/admin/media/icn_pdf.gif',
            'thumbWidth' => 65,
        );
    }

    public function test_findAllMediaInfos()
    {
        $expected = array(
            $this->_getExpectedOfJpeg(),
            $this->_getExpectedOfPng(),
            $this->_getExpectedOfPdf(),
        );

        $this->assertEquals($expected, $this->_media->findAllMediaInfos());
    }

}
