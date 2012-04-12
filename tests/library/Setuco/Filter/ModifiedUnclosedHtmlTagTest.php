<?php
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * Setuco_Filter_ModifiedUnclosedHtmlTagのテストクラス
 *
 * @author suzuki-mar
 */
class ModifiedUnclosedHtmlTagTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->filter = new Setuco_Filter_ModifiedUnclosedHtmlTag();
    }

    public function testFilter_タグが閉じていないければ閉じていないタグを閉じる()
    {
        $this->assertSame($this->filter->filter('<div>ほげ<p>fuga<!-- hoge --></div>'), '<div>ほげ<p>fuga<!-- hoge --></p></div>');
    }

    public function testFilter_タグがひとつだけの場合()
    {
        $this->assertSame($this->filter->filter('<p>hoge'), '<p>hoge</p>');
    }

    public function testFilter_タグではない場合はそのまま帰す()
    {
        $this->assertSame($this->filter->filter('hoge'), 'hoge');
    }

    public function testFilter_すべてのタグがとじている場合はそのまま帰す()
    {
       $this->assertSame($this->filter->filter('<a>fuga</a>'), '<a>fuga</a>');
    }
}

