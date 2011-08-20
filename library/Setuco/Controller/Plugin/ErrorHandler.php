<?php

/**
 * エラーコントローラーを制御するプラグインです。
 * このプラグインで、モジュールごとに使用するエラーコントローラーを変更します。
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
 * @subpackage Controller_Plugin
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @package     Setuco
 * @subpackage  Controller_Plugin
 * @author      suzuki-mar
 */
class Setuco_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_Abstract
{

    /**
     * ディスパッチする前に実行するメソッド
     * エラーコントローラーの種類を変更する
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        //フロントコントローラーに登録されているエラープラグインを取得して、設定する
        $errorHanlder = Zend_Controller_Front::getInstance()->getPlugin("Zend_Controller_Plugin_ErrorHandler");
        //モジュールごとにエラーコントローラークラスを使用する
        $errorHanlder->setErrorHandlerModule($request->getModuleName());
    }

}
