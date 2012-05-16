<?php

require_once "../bootstrap.php";

class PageTest extends Setuco_Test_PHPUnit_SeleniumTestCase
{
    public function test_タイトルをAND条件で検索できるか()
    {
        $this->_login();
        $this->open("/admin/page");

        $this->type("id=query", "タイトル 検索して");
        $this->click("id=sub_search");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent("タイトルで検索して"));
    }

    public function test_概要をAND条件で検索できるか()
    {
        $this->_login();
        $this->open("/admin/page");

        $this->type("id=query", "アウトライン 検索して");
        $this->click("id=sub_search");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent("アウトラインで検索して"));
    }

    public function test_コンテンツをAND条件で検索できるか()
    {
        $this->open("/admin/page");
        $this->type("id=query", "コンテンツ");
        $this->click("id=sub_search");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent("コンテンツで検索して"));
    }




}

