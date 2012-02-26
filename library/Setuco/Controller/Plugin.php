<?php

/**
 * Setucoの標準コントローラプラグイン
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
 * @subpackage Controller
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     Yuu Yamanaka, charlelsvineyard
 */

/**
 * @package    Setuco
 * @subpackage Controller
 * @author     Yuu Yamanaka, charlelsvineyard
 */
class Setuco_Controller_Plugin extends Zend_Controller_Plugin_Abstract
{
    /**
     * ディスパッチループの初期処理です。
     * 
     * @return void
     * @author Yuu Yamanaka, charlesvineyard
     */
    public function dispatchLoopStartup()
    {
        if ($this->getRequest()->getModuleName() == 'admin') {
            if ($this->_isLoginControllerRequested()) {
                return;
            }
            if (! $this->_isLoggedIn()) {
                $this->_redirectLogin();        
            }
            $this->_setNavigationEnable();
        }
    }
    
    /**
     * ログインコントローラにアクセスが来ているかを判断します。
     * 
     * @return boolean アクセスされていれば true
     * @author charlesvineyard
     */
    private function _isLoginControllerRequested()
    {
        return $this->getRequest()->getControllerName() == 'login';
    }
    
    /**
     * ログイン状態かどうかを判断します。
     * 
     * @return boolean ログイン状態なら true
     * @author charlesvineyard
     */
    private function _isLoggedIn()
    {
        return Zend_Auth::getInstance()->hasIdentity();
    }
    
    /**
     * ナビゲーションを有効にします。
     * 
     * @return void
     * @author charlesvineyard
     */
    private function _setNavigationEnable()
    {
        $actionStack = new Zend_Controller_Action_Helper_ActionStack();
        $actionStack->actionToStack('navigation', 'navigation');
    }
    
    /**
     * ログイン画面にリダイレクトします。
     * 
     * @return void
     * @author charlesvineyard
     */
    private function _redirectLogin()
    {
        $redirector = Zend_Controller_Action_HelperBroker::
                getStaticHelper('redirector');
        $redirector->goToSimple('index', 'login', 'admin');
    }

}