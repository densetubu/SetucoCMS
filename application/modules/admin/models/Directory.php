<?php
/**
 * 管理側のサイト構造用サービス
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * サイト構造管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     charlesvineyard
 */
class Admin_Model_Directory
{
    /**
     * カテゴリーDAO
     *
     * @var Common_Model_DbTable_Category
     */
    private $_category;

    /**
     * ページDAO
     *
     * @var Common_Model_DbTable_Page
     */
    private $_page;

    /**
     * コンストラクタ
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_category = new Common_Model_DbTable_Category();
        $this->_page = new Common_Model_DbTable_Page();
    }

    /**
     * サイト構造の情報を作成します。
     *
     * @return Zend_Navigation
     * @author charlesvineyard
     */
    public function createDirectoryInfo()
    {
        $directory = new Zend_Navigation();
        $categories = $this->_category->findCategoriesByParentId(Setuco_Data_Constant_Category::NO_PARENT_ID);
        foreach ($categories as $category) {
            $directory->addPage($this->_createNavCategory($category['id'], $category['name']));
        }
        return $directory;
    }

    /**
     * カテゴリー情報を作成します。
     *
     * @return Zend_Navigation_Page カテゴリー情報
     * @author charlesvineyard
     */
    private function _createNavCategory($categoryId, $categoryName)
    {
        $navCategory = Zend_Navigation_Page::factory(array(
            'label'      => $categoryName,
            'module'     => 'admin',
            'controller' => 'page',
            'params'     => array('category-id' => $categoryId)
        ));
        $navCategory->addPages($this->_createNavPages($categoryId));
        return $navCategory;
    }

    /**
     * カテゴリーに属するページ情報を作成します。
     *
     * @return array Zend_Navigation_Pageの配列
     * @author charlesvineyard
     */
    private function _createNavPages($categoryId)
    {
        $pages = $this->_page->findPagesByCategoryId($categoryId);
        $navPages = array();
        foreach ($pages as $page) {
            $navPages[] = Zend_Navigation_Page::factory(array(
                'label'      => $page['title'],
                'module'     => 'admin',
                'controller' => 'page',
                'params'     => array('id' => $page['id']),
                'type'       => 'Setuco_Navigation_Page_Directory_Page'
                ));
        }
        return $navPages;
    }

}
