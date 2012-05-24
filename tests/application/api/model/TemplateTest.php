<?php

/**
 *
 * @author suzuki-mar
 */

//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}


class Api_Model_TemplateTest extends Setuco_Test_PHPUnit_DatabaseTestCase
{
 
    public function setup()
    {
        parent::setup();

        $this->_template = new Api_Model_Template($this->getAdapter());
    }

    public function test_findAllTemplateInfos_テンプレートデータを登録する()
    {
        $expected = array (

          array (
            'title' => 'TOPページのテンプレート',
            'explanation' => 'TOPページのテンプレートです',
            'url' => 'http://setucocms.localdomain/upload/template/1.html',
          ),

          array (
            'title' => '一般ユーザーのテンプレート',
            'explanation' => '一般ユーザーが作成したテンプレートです',
            'url' => 'http://setucocms.localdomain/upload/template/2.html',
          ),
        );

        $this->assertEquals($expected, $this->_template->findAllTemplateInfos());
        
    }

}
