<?php

require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .   'bootstrap.php';

/**
 * Setuco_Test_PHPUnit_DataSetのテストクラス
 *
 * @author suzuki-mar
 */
class DataSetTestCase extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->dataSet = new Setuco_Test_PHPUnit_DataSet();
    }


    public function testAddTables_Fixtureクラスからデータを作成する()
    {
        $expects = array (
               'account' => array(
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
               ),
        );

        $this->assertEquals($expects, $this->dataSet->addTables(array('account')));
    }

    public function testAddTables_ファイルが存在しない場合のエラー処理()
    {
        $this->setExpectedException('InvalidArgumentException', 'dir/not_exists_file.phpというフィクスチャーファイルはありません');

        $dataSet = new Setuco_Test_PHPUnit_ClassDataSet_Test();
        
        $dataSet->addTables(array('not_exists_file'));
    }

}

class Setuco_Test_PHPUnit_ClassDataSet_Test extends Setuco_Test_PHPUnit_DataSet
{
    protected function _getFixtureBasePath()
    {
       return 'dir/';
    }
}