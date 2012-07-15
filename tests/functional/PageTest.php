<?php

require_once "bootstrap.php";

class PageTest extends Setuco_Test_PHPUnit_SeleniumTestCase
{
    /**
     * フィクスチャーを使用することができるようになるまで、
     * テストはしない
     */

//    public function test_タイトルをAND条件で検索できるか()
//    {
//        $this->_login();
//        $this->open("/admin/page");
//
//        $this->type("id=query", "タイトル 検索して");
//        $this->click("id=sub_search");
//        $this->waitForPageToLoad("30000");
//        $this->assertTrue($this->isTextPresent("タイトルで検索して"));
//
//        $this->assertCountItemList(1);
//    }

}