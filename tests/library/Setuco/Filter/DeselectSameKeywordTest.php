<?php
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * Setuco_Filter_DeselectSameKeywordのテストクラス
 *
 * @author suzuki-mar
 */
class DeselectSameKeywordTest extends PHPUnit_Framework_TestCase {


    public function setup()
    {
        $this->filter = new Setuco_Filter_DeselectSameKeyword();

    }

    /**
     * フィルターが正常に行われるかをテストする
     *
     */
    public function testFilter()
    {
        $this->assertEquals($this->filter->filter('aaa,aaa'), 'aaa', '同じキーワードだけ');
        $this->assertEquals($this->filter->filter('aaa, aaa'), 'aaa', 'スペース付きの同じキーワードだけ');
        $this->assertEquals($this->filter->filter(''), '', 'キーワードがない');

        $this->assertEquals($this->filter->filter('aaa,bbb'), 'aaa,bbb', '違うキーワードだけ');
        $this->assertEquals($this->filter->filter('aaa,bbb,aaa'), 'aaa,bbb', '同じキーワードだけ除外できているか');
    }
    
}

