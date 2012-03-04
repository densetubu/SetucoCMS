<?php
/**
 * 管理側のサイト構造用サービス
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
    private $_categoryDao;

    /**
     * ページDAO
     *
     * @var Common_Model_DbTable_Page
     */
    private $_pageDao;

    /**
     * コンストラクタ
     *
     * @author charlesvineyard
     */
    public function __construct()
    {
        $this->_categoryDao = new Common_Model_DbTable_Category();
        $this->_pageDao = new Common_Model_DbTable_Page();
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
        $categories = $this->_categoryDao->loadCategoriesByParentId(Setuco_Data_Constant_Category::NO_PARENT_ID, 'name');
        foreach ($categories as $category) {
            $directory->addPage($this->_createNavCategory($category['id'], $category['name']));
        }
        // 未分類を追加
        $directory->addPage($this->_createNavCategory(null, Setuco_Data_Constant_Category::UNCATEGORIZED_STRING));
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
            'controller' => 'directory',
            'params'     => array(
                'category_id' => Setuco_Data_Converter_CategoryInfo::convertCategoryId4View($categoryId)
            )
        ));
        $navCategory->addPages($this->_createNavPages($categoryId));
        return $navCategory;
    }

    /**
     * カテゴリーに属するページ情報を作成します。
     *
     * @param  int categoryId カテゴリーID
     * @return array Zend_Navigation_Pageの配列
     * @author charlesvineyard
     */
    private function _createNavPages($categoryId)
    {
        $pages = $this->_pageDao->loadPagesByCategoryId4Pager($categoryId);
        $navPages = array();
        foreach ($pages as $page) {
            $navPages[] = Zend_Navigation_Page::factory(array(
                'label'      => $page['title'],
                'module'     => 'admin',
                'controller' => 'page',
                'params'     => array('id' => $page['id']),
                'visible'     => $page['status'] == Setuco_Data_Constant_Page::STATUS_RELEASE ? true : false,
                'type'       => 'Setuco_Navigation_Page_Directory_Page'
                ));
        }
        return $navPages;
    }

}
