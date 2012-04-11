<?php
require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  '..' . DIRECTORY_SEPARATOR .  'bootstrap.php';

/**
 * Setuco_Contents_Convertのテストクラス
 *
 * @author suzuki-mar
 */
class ConvertTest extends PHPUnit_Framework_TestCase
{

    public function setup()
    {
        $this->converter = new Setuco_Contents_Convert();

        $this->_baseEntry = array (
            'id' => '10', 'title' => '長い続きを読む',
            'outline' => '', 'status' => '1', 'category_id' => NULL, 'account_id' => '1',
            'create_date' => '2012-04-11 09:49:01', 'update_date' => '2012-04-11 09:49:01',
            'nickname' => '管理者');

    }

    public function testConvert_記事を変換する()
    {
        $pageBreak = new Setuco_Contents_Convert_PageBreak();
        $pageBreak->setBaseUrl('http://setucocms.localdomain/page/category');

        $this->converter->addElement($pageBreak);

        $entry = $this->_baseEntry;

        #301文字
        $firstString = 'あいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえ';
        $lastString = 'おあいうえおあいうえおあいうえおあ';

        $entry['contents'] = "{$firstString}<!-- pagebreak -->{$lastString}";

        $convertedEntry = $entry;

        $convertedEntry['contents'] = "{$firstString}<a href=\"http://setucocms.localdomain/page/category/id/10\">もっと読む</a>";


        $this->assertSame($this->converter->convert($entry), $convertedEntry);
    }

    public function testConvert_エレメントを追加していない場合はなにもしない()
    {
        $entry = $this->_baseEntry;
        $entry['contents'] = 'test';

        $this->assertSame($this->converter->convert($entry), $entry);
    }
    
}

