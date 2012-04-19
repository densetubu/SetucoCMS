<?php

require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .   'bootstrap.php';

/**
 * Setuco_Filter_DeselectSameKeywordのテストクラス
 *
 * @author suzuki-mar
 */
class ClassDataSetTestCase extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->dataSet = new Setuco_Test_PHPUnit_DataSet();
    }



    public function testAddTable_クラスの定義からfixtureを作成する()
    {
        $expects = array (
          'columns' => array ('id', 'login_id', 'nickname', 'password'),
          'fixtures' =>
              array (
                array (
                  'id' => '1',
                  'login_id' => 'admin',
                  'nickname' => 'setuco',
                  'password' => 'pass',
                ),
                array (
                  'id' => '2',
                  'login_id' => 'user',
                  'nickname' => 'setuo',
                  'password' => 'pass',
                ),
                array (
                  'id' => '3',
                  'login_id' => 'search',
                  'nickname' => '検索する人',
                  'password' => 'pass',
                ),
              ),
        );

        $this->assertEquals($expects, $this->dataSet->addTable('account'));
    }

    public function testAddTable_ファイルが存在しない場合のエラー処理()
    {
        $this->setExpectedException('InvalidArgumentException', 'dir/not_exists_file.phpというフィクスチャーファイルはありません');

        $dataSet = new Setuco_Test_PHPUnit_ClassDataSet_Test();
        
        $dataSet->addTable('not_exists_file');
    }

}

class Setuco_Test_PHPUnit_ClassDataSet_Test extends Setuco_Test_PHPUnit_DataSet
{
    protected function _getFixtureBasePath()
    {
       return 'dir/';
    }
}