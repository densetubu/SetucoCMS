<?php

/**
 * エラーコントローラーを制御するプラグインです。
 * このプラグインで、モジュールごとに使用するエラーコントローラーを変更します。
 *
 * LICENSE: ライセンスに関する情報
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
 * @category    Setuco
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
