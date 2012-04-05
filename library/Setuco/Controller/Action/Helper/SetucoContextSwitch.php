<?php

/**
 * コンテキストを制御するプラグインです。
 * このヘルパーで、JSONやXMLに対応するコンテキストを設定します
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
 * @subpackage Controller_Action_Helper
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @package     Setuco
 * @subpackage  Controller_Action_Helper
 * @author      suzuki-mar
 */
class Setuco_Controller_Action_Helper_SetucoContextSwitch extends Zend_Controller_Action_Helper_ContextSwitch
{

    /**
     * viewの変数をJSON形式にして出力する
     *
     * 変数がひとつしか渡されなかったらその変数だけを出力する(配列ではなくて)のを実装するために、
     * オーバーライドしている
     *
     *
     * @author suzuki-mar
     * @throws viewがgetVarsのメソッドがなかったら例外を発生する
     */
    public function postJsonContext()
    {
        if (!$this->getAutoJsonSerialization()) {
            return;
        }

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;

        if ($view instanceof Zend_View_Interface) {
            /**
             * @see Zend_Json
             */
            if(method_exists($view, 'getVars')) {

                #変数がひとつだけの場合は配列にしない
                if (count($view->getVars()) === 1 ) {
                    $viewData = array_shift($view->getVars());
                    $vars = Zend_Json::encode($viewData);
                } else {
                    $vars = Zend_Json::encode($view->getVars());
                }

                $this->getResponse()->setBody($vars);
            } else {
                throw new Zend_Controller_Action_Exception('View does not implement the getVars() method needed to encode the view into JSON');
            }
        }
    }
}
