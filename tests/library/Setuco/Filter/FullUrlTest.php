<?php
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * 
 *
 * @author suzukimasayuki
 */
class FullUrlTest extends PHPUnit_Framework_TestCase {

    /**
     * テストするフィルタークラス
     *
     * @var Setuco_Filter_FullUrl
     */
    private $_filter;

    public function setup() {
        $this->_filter = new Setuco_Filter_FullUrl();
    }

    /**
     * フィルターが出来ているかをテストする
     * 
     */
    public function testFilter() {
       $this->assertEquals($this->_filter->filter('hogehoge'), 'http://hogehoge', 'httpを付けているか');
       $this->assertEquals($this->_filter->filter('https://hogehoge'), 'https://hogehoge', 'httpsのままか');
       $this->assertEquals($this->_filter->filter('http://hogehoge'), 'http://hogehoge', 'httpが付いている場合は、httpをつけないか');
    }
}

