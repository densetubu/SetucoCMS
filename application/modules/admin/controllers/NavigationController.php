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
        $navigationList =  array();
        foreach ($this->_navigation as $page) {
            $page = (object) $page;
            $childList = array();
            foreach ($page->pages as $childPage) {
                if ($childPage->visible) {
                    $child['id']       = $childPage->id;
                    $child['label']    = $childPage->label;
                    $child['href']     = $childPage->getHref();
                    $child['isActive'] = $childPage->active;
                    $childList[] = $child;
                }
            }
            $navigation = array();
            if ($page instanceof Setuco_Navigation_Page) {
                $navigation['src'] = $this->view->baseUrl($page->getSrc());
            }
            $navigation['id']         = $page->id;
            $navigation['label']      = $page->label;
            $navigation['href']       = $page->getHref();
            $navigation['isActive']   = $page->active;
            $navigation['childList']  = $childList;
            $navigationList[] = $navigation;
        }
        $this->view->navigationList = $navigationList;

        $this->_helper->viewRenderer->setResponseSegment('navigation');
    }
}
