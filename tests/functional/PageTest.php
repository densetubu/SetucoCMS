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
        $this->assertTrue($this->isTextPresent(Fixture_Page::TITLE_TITLE));

        $this->assertCountItemList(1);
    }

    public function test_アウトラインをAND条件で検索できるか()
    {
        $this->_login();
        $this->open("/admin/page");

        $this->type("id=query", "アウトライン");
        $this->click("id=sub_search");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent(Fixture_Page::OUTLINE_TITLE));

        $this->assertCountItemList(1);
    }

    public function test_コンテンツをAND条件で検索できるか()
    {
        $this->open("/admin/page");
        $this->type("id=query", "コンテンツ　検索");
        $this->click("id=sub_search");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent(Fixture_Page::CONTENTS_TITLE));

        $this->assertCountItemList(1);
    }

    public function test_タグをAND条件で検索する()
    {
        $this->open("/admin/page");
        $this->type("id=query", "setuco test");
        $this->click("id=sub_search");
        $this->waitForPageToLoad("30000");
        $this->assertTrue($this->isTextPresent(Fixture_Page::TAG_TITLE));

        $this->assertCountItemList(1);
    }

    /**
     * @group now
     */
    public function test_全ての項目から検索する()
    {
        $this->open("/admin/page");
        $this->type("id=query", "検索して");
        $this->click("id=sub_search");
        $this->waitForPageToLoad("30000");

        $titles = array(
            Fixture_Page::TITLE_TITLE,
            Fixture_Page::CONTENTS_TITLE,
            Fixture_Page::OUTLINE_TITLE,
            Fixture_Page::ACCOUNT_TITLE,
            Fixture_Page::ACCOUNT_ONLY_TITLE,
        );

        $this->assertCountItemList(5);
    }

    

}

