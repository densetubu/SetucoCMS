<?php

/**
 *
 * @author suzuki-mar
 */
//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

class Common_DbTable_MediaTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{

    public function setup()
    {
        parent::setup();
        $this->_dao = new Common_Model_DbTable_Media($this->getAdapter());
    }

    public function test_loadImageMedias_画像のレコードを取得する()
    {

        $expected = array(
            array(
                'id' => '1',
                'name' => 'image.jpeg',
                'type' => 'jpg',
                'create_date' => '2012-04-11 08:42:09',
                'update_date' => '2012-04-11 08:42:09',
                'comment' => '2012-04-11 08:42:09にアップロード',
            ),
            array(
                'id' => '2',
                'name' => 'image.png',
                'type' => 'png',
                'create_date' => '2012-04-11 08:42:09',
                'update_date' => '2012-04-11 08:42:09',
                'comment' => '2012-04-11 08:42:09にアップロード',
            ),
        );

        $this->assertEquals($expected, $this->_dao->loadImageMedias());
    }

    public function test_loadEtcMedias_画像以外のレコードを取得する()
    {
        $expected = array(
              array (
                'id' => '3',
                'name' => 'sample.pdf',
                'type' => 'pdf',
                'create_date' => '2012-04-11 08:42:09',
                'update_date' => '2012-04-11 08:42:09',
                'comment' => '2012-04-11 08:42:09にアップロード',
              ),
        );

        $this->assertEquals($expected, $this->_dao->loadEtcMedias());

    }

}

