<?php
/**
 * ナビゲーションページの拡張クラスです。
 *
 * Zend_Navigation_Page_Mvcのプロパティに加えてHrefを２種類取得する機能を持ちます。
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
 * @package    Setuco
 * @subpackage Navigation_Page_Directory
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     charlelsvineyard
 */

/**
 * @package    Setuco
 * @subpackage Navigation_Page_Directory
 * @author     charlelsvineyard
 */
class Setuco_Navigation_Page_Directory_Page extends Zend_Navigation_Page_Mvc
{
    /**
     * 編集用のURLを取得します。
     *
     * @return string 編集用のURL
     * @author charlesvineyard
     */
    public function getEditHref()
    {
        // hrefを構成するための設定
        $module = $this->getModule();
        $this->setModule('admin');
        $controller = $this->getModule();
        $this->setController('page');
        $action = $this->getAction();
        $this->setAction('index');

        $href = $this->getHref();

        // 元に戻す
        $this->setModule($module);
        $this->setController($controller);
        $this->setAction($action);

        return $href;
    }

    /**
     * 閲覧用のURLを取得します。
     * 閲覧不可であればfalseを返します。
     *
     * @return mixed 閲覧用のURL or 閲覧不可時は false
     * @author charlesvineyard
     */
    public function getViewHref()
    {
        if (! $this->_visible) {
            return false;
        }

        // hrefを構成するための設定
        $module = $this->getModule();
        $this->setModule('default');
        $controller = $this->getModule();
        $this->setController('page');
        $action = $this->getAction();
        $this->setAction('show');

        $href = $this->getHref();

        // 元に戻す
        $this->setModule($module);
        $this->setController($controller);
        $this->setAction($action);

        return $href;
    }

}
