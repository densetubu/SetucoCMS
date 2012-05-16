<?php

//AllTestsなどでbootstrapをすでに読み込んでいるかもしれない
if (!defined('BOOT_STRAP_FINSHED')) {
    require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

/**
 * Setuco_Fixture_Holderのテストクラス
 *
 * @author suzuki-mar
 */
class Setuco_Sql_HolderTest extends PHPUnit_Framework_TestCase
{

    public function testAddTables_ファイルが存在しない場合は例外を発生させる()
    {
        $this->setExpectedException('InvalidArgumentException', 'dir/not_exists_file.phpというフィクスチャーファイルはありません');
        $dataSet = new Setuco_Fixture_Holder_Mock();
        $dataSet->createFixtureInstanceByTableName(array('not_exists_file'));
    }

}

class Setuco_Fixture_Holder_Mock extends Setuco_Fixture_Holder
{

    protected function _getFixtureBasePath()
    {
        return 'dir/';
    }

}

