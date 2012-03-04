<?php
/**
 * ナビゲーションのコントローラ
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
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * ナビゲーションのコントローラ
 *
 * @package    Admin
 * @subpackage Controller
 * @author     charlesvineyard
 */

class Admin_NavigationController extends Setuco_Controller_Action_AdminAbstract
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
