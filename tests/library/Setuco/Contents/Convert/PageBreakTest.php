<?php
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  '..' . DIRECTORY_SEPARATOR .  'bootstrap.php';

/**
 * Setuco_Contents_Convert_PageBreakのテストクラス
 *
 * @author suzuki-mar
 */
class PageBreakTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->converter = new Setuco_Contents_Convert_PageBreak();
        $this->converter->setBaseUrl('http://setucocms.localdomain/page/category');

        $this->converter_change_params = new Setuco_Contents_Convert_PageBreak();
        $this->converter_change_params->setBaseUrl('http://setucocms.localdomain/page/category');
        $this->converter_change_params->setPageBreakString('more');
        $this->converter_change_params->setReplacementString('MOTTOMOTTO読む');
        $this->converter_change_params->setReplaceStringLength(3);


        $this->_baseEntry = array (
            'id' => '10', 'title' => '長い続きを読む',
            'outline' => '', 'status' => '1', 'category_id' => NULL, 'account_id' => '1',
            'create_date' => '2012-04-11 09:49:01', 'update_date' => '2012-04-11 09:49:01',
            'nickname' => '管理者');

    }

    public function testConvert_記事を変換する()
    {
        $entry = $this->_baseEntry;

        #301文字
        $firstString = <<<EOF
<p>あいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあい</p>
    
EOF;

        $lastString = <<<EOF
<p>&nbsp;</p>
<p>あいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいう</p>
EOF;

        $entry['contents'] = "{$firstString}<p><!-- pagebreak --></p>{$lastString}";

        $convertedEntry = $entry;

        $convertedEntry['contents'] = "{$firstString}<p><a href=\"http://setucocms.localdomain/page/category/id/10\">もっと読む</a>";

        $this->assertSame($this->converter->convert($entry), $convertedEntry);
    }

    public function testConvert_短い文章の場合は変換しないか()
    {
        $entry = $this->_baseEntry;
        $entry['contents'] = "てす<!-- more -->";

        $this->assertSame($this->converter_change_params->convert($entry), $entry);
    }

    public function testConvert_置換用のパラメータを変換できるか()
    {
        $entry = $this->_baseEntry;
        $entry['contents'] = "てすとｔ<!-- more -->123";

        $convertedEntry = $entry;
        $convertedEntry['contents'] = 'てすとｔ<a href="http://setucocms.localdomain/page/category/id/10">MOTTOMOTTO読む</a>';

        $this->assertSame($this->converter_change_params->convert($entry), $convertedEntry);
    }
    
}

