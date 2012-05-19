<?php

/* 
 * テストのexpectedをFixtureクラスから作成するクラス
 *
 *
 * @author suzuki-mar
 */
class CreateExpected
{
    /**
     *
     * @var Fixture_Page
     */
    private $_pageFixture;

    /**
     *
     * @var Fixture_Category
     */
    private $_categoryFixture;

    /**
     *
     * @var Fixture_Account
     */
    private $_accountFixture;

    /**
     *
     * @var Fixture_Tag
     */
    private $_tagFixture;


    public function  __construct()
    {
        $this->_pageFixture = new Fixture_Page();
        $this->_categoryFixture = new Fixture_Category();
        $this->_accountFixture = new Fixture_Account();
        $this->_tagFixture = new Fixture_Tag();
    }

    public function createPageDataByPageId($id)
    {
        $base = $this->_pageFixture->getFixtureBase();

        switch ($id) {
            case Fixture_Page::TITLE_ID:
                $data = $this->_pageFixture->getDataOfTitle();
                break;

            case Fixture_Page::MULTI_KEYWORD_ID:
                $data = $this->_pageFixture->getDataOfMulti_Keyword();
                break;

            case Fixture_Page::CONTENTS_ID:
                $data = $this->_pageFixture->getDataOfContents();
                break;

            case Fixture_Page::OUTLINE_ID:
                $data = $this->_pageFixture->getDataOfOutline();
                break;

            case Fixture_Page::TAG_ID:
                $data = $this->_pageFixture->getDataOfTag();
                break;

            case Fixture_Page::ACCOUNT_ID:
                $data = $this->_pageFixture->getDataOfAccount();
                break;

            case Fixture_Page::ACCOUNT_ONLY_ID:
                $data = $this->_pageFixture->getDataOfAccount_Only();
                break;

            case Fixture_Page::HTML_TAG_ID:
                $data = $this->_pageFixture->getDataOfHtml_Tag();
                break;

            case Fixture_Page::NO_HTML_TAG_ID:
                $data = $this->_pageFixture->getDataOfNotag();
                break;

        }

        return array_merge($base, $data);
    }

    public function createTagIdsByKeyword($keyword)
    {
        $words = Setuco_Util_String::convertArrayByDelimiter($keyword);

        $ids = array();
        foreach ($this->_tagFixture->getDatas() as $data) {
            if (in_array($data['name'], $words)) {
                $ids[] = $data['id'];
            }
        }

        return $ids;
    }

    public function createTagNamesByTagIds(array $tagIds)
    {
        $names = array();

        foreach ($this->_tagFixture->getDatas() as $data) {
            if (in_array($data['id'], $tagIds)) {
                $names[] = $data['name'];
            }
        }

        return $names;
    }

    public function createCategoryNameByCategoryId($categoryId)
    {
        switch ($categoryId) {
            case Fixture_Category::ROOT_ID:
                $categoryData = $this->_categoryFixture->getDataOfRoot();
                break;

            case Fixture_Category::TEST_ID:
                $categoryData = $this->_categoryFixture->getDataOfTest();
                break;
        }

        if (!isset($categoryData)) {
            return null;
        }

        return $categoryData['name'];
    }

    public function createNickNameByAccountId($accountId)
    {
        switch ($accountId) {
            case Fixture_Account::ADMIN_ID:
                $accountData = $this->_accountFixture->getDataOfAdmin();
                break;

            case Fixture_Account::GENERAL_ID:
                $accountData = $this->_accountFixture->getDataOfGeneral();
                break;

            case Fixture_Account::TARGET_ID:
                $accountData = $this->_accountFixture->getDataOfTarget();
                break;

        }

        return $accountData['nickname'];
    }
}

