<?php
/**
 * ナビゲーションのコントローラーです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category Setuco
 * @package Admin
 * @subpackage Controller
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     charlelsvineyard
 */

/**
 * @category Setuco
 * @package  Admin
 * @subpackage Controller
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author  charlelsvineyard
 */
class Admin_NavigationController extends Setuco_Controller_Action_Admin
{
    /**
     * ナビゲーションのアクションです。
     * 
     * @return void
     * @author charlesvineyard
     */
    public function navigationAction()
    {
        $navInfos =  array();
        foreach ($this->_navigation as $parentNavPage) {
            $childNavInfos = $this->_createChildNavInfos($parentNavPage);
            $navInfos[] = $this->_createNavInfo($parentNavPage, $childNavInfos);
        }
        $this->view->navInfos = $navInfos;
        $this->_helper->viewRenderer->setResponseSegment('navigation');
    }
    
    /**
     * 親ナビゲーションページに属する全ての子ページの情報を作成します。
     * 
     * @param  Zend_Navigation_Page 親ナビゲーションページ
     * @return array 全ての子ページ情報の配列
     */
    private function _createChildNavInfos($parentNavPage)
    {
        $childNavInfos = array();
        foreach ($parentNavPage->pages as $child) {
            if ($child->visible) {
                $childNavInfo['id']       = $child->id;
                $childNavInfo['label']    = $child->label;
                $childNavInfo['href']     = $child->getHref();
                $childNavInfo['isActive'] = $child->active;
                $childNavInfos[] = $childNavInfo;
            }
        }
        return $childNavInfos;
    }
    
    /**
     * 親ナビゲーションページとそれに属するページの情報から
     * １つのナビゲーション情報を作成します。
     * 
     * @param Zend_Navigation_Page $parentNavPage 親ナビゲーションページ
     * @param array $childNavPages 親に属するページのナビゲーション情報
     */
    private function _createNavInfo($parentNavPage, $childNavPages)
    {
        $navInfo = array();
        if ($parentNavPage instanceof Setuco_Navigation_Page) {
            $navInfo['src'] = $this->view->baseUrl($parentNavPage->getSrc());
        }
        $navInfo['id']          = $parentNavPage->id;
        $navInfo['label']       = $parentNavPage->label;
        $navInfo['href']        = $parentNavPage->getHref();
        $navInfo['isActive']    = $parentNavPage->active;
        $navInfo['childrenArr'] = $childNavPages;
        return $navInfo;        
    }
}
