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
     * サイト構造の情報をロードします。
     * 
     * @return Zend_Navigation
     * @author charlesvineyard
     */
    public function load()
    {
        $directory = new Zend_Navigation();
        $categories = $this->_loadCategoriesByParentId(null);
        foreach ($categories as $category) {
            $navCategory = Zend_Navigation_Page::factory(array(
                'label'      => $category['categoryName'],
                'module'     => 'admin',
                'controller' => 'page',
                'params'     => array('category-id' => $category['categoryId'])
            ));
            $directory->addPage($navCategory);
            $pages = $this->_loadPages($category['categoryId']);
            foreach ($pages as $page) {
                $navCategory->addPage(array(
                    'label'      => $page['pageTitle'],
                    'module'     => 'admin',
                    'controller' => 'page',
                    'params'     => array('id' => $page['pageId']),
                    'type'       => 'Setuco_Navigation_Page_Directory_Page'
                ));
            }
        }
        return $directory;
    }
    
    /**
     * 指定の親カテゴリーIDを持つカテゴリーをロードします。
     * 
     * @param int $parentId
     * @return array カテゴリーの配列
     * @author charlesvineyard
     * @todo Category クラスに移すべきメソッド
     */
    private function _loadCategoriesByParentId($parentId)
    {
        if ($parentId == null) {
            return array(
                array('categoryId'   => 1,
                      'categoryName' => 'カテゴリー1',
                      'parentId'     => null),
                array('categoryId'   => 3,
                      'categoryName' => 'カテゴリー3',
                      'parentId'     => null),
                array('categoryId'   => 4,
                      'categoryName' => 'カテゴリー4',
                      'parentId'     => null),
            );

        }
        
        if ($parentId == 1) {
            return array(
                array('categoryId'   => 2,
                      'categoryName' => 'カテゴリー2',
                      'parentId'     => 1),
            );
        }
        
        return array();
    }
    
    /**
     * 指定のカテゴリーIDを持つページをロードします。
     * 
     * @param int $categoryId
     * @return array ページの配列
     * @author charlesvineyard
     * @todo Page クラスに移すべきメソッド
     */
    private function _loadPages($categoryId)
    {
        return array(
            array('pageId'    => 1,
                  'pageTitle' => 'ページタイトル',
                  'status'    => 1),
            array('pageId'    => 1,
                  'pageTitle' => 'ページタイトル',
                  'status'    => 1),
            array('pageId'    => 1,
                  'pageTitle' => 'ページタイトル',
                  'status'    => 0)
        );        
    }

}

