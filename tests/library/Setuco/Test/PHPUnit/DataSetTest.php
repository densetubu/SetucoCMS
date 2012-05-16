<?php
//bootstarapを複数回読み込まないようにするため
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .   'bootstrap.php';
}


/**
 * Setuco_Test_PHPUnit_DataSetのテストクラス
 *
 * @author suzuki-mar
 */
class DataSetTest extends PHPUnit_Framework_TestCase
{

    const PASS_HASH = '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8';

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
                          'password' => self::PASS_HASH,
                        ),
                        array (
                          'id' => '2',
                          'login_id' => 'user',
                          'nickname' => 'setuo',
                          'password' => self::PASS_HASH,
                        ),
                        array (
                          'id' => '3',
                          'login_id' => 'search',
                          'nickname' => '検索する人',
                          'password' => self::PASS_HASH,
                        ),
                      ),
               ),
        );

        $this->assertEquals($expects, $this->dataSet->addTables(array('account')));
    }

}