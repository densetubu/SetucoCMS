<?php
/**
 * エラーコントローラーの継承クラス
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage Controller_Action
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
 * @subpackage  Controller_Action
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_ErrorAbstract extends Setuco_Controller_Action_Abstract
{
    /**
     * スクリプト(view)ファイルを、本番環境用に変更する
     * Not Found用とサーバーエラー用の２種類のviewファイルがある
     *
     * @return void
     * @author suzuki-mar
     */
    protected function _changeErrorRender()
    {
        $errors    = $this->_getParam('error_handler');
        $errorCode = $this->_getParam('errorCode', $this->_getErrorResponeByErrorType($errors->type));

        switch ($errorCode) {
            case 404:
                $viewFile = 'error_404';
                break;

            default:
                $viewFile = 'error_default';
                break;
        }

        $this->_helper->viewRenderer->setScriptAction($viewFile);
    }

    /**
     * HTTPリクエストにエラーコードレスポンスを設定する
     * 
     * @return void
     * @author suzuki-mar
     */
    protected function _setErrorResponeCode()
    {
        $errors = $this->_getParam('error_handler');

        $errorCode = $this->_getParam('errorCode', $this->_getErrorResponeByErrorType($errors->type));
        $this->getResponse()->setHttpResponseCode($errorCode);
    }


    /**
     * error_handlerは、ZFのエラーコントローラーに標準に設定されているパラメーター
     * エラータイプ(error_handelr->type)からレスポンスコードを求める
     *
     * @param string $errorType error_handelr->type
     * @return int レスポンスコード
     * @author suzuki-mar
     */
    protected function _getErrorResponeByErrorType($errorType)
    {
        switch ($errorType) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                $result = 404;
                break;
            default:
                $result = 500;
                break;
        }

        return $result;
    }

    /**
     * オリジナルのエラーメッセージをviewにセットする
     * ZFの標準のスクリプトファイルで必要なものを設定する
     *
     * @return void
     * @author suzuki-mar
     */
    protected function _setDefaultErrorParamsForView()
    {

        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:                
                $this->view->message = 'Page not found';
                break;
            
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }

        //ログが有効になっている場合は、ログの例外メッセージをセットする
        $log = $this->_getErrorLog();
        if ($log !== false) {
            $log->crit($this->view->message, $errors->exception);
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    /**
     * ログのプラグインリソースが有効になっているときに取得する
     *
     * @return Logのリソース  有効でない場合はfalse
     * @author suzuki-mar
     */
    protected function _getErrorLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }

    /**
     * モジュール毎に違うレイアウトを表示する
     * エラーコントローラーは、レイアウトが無効になっている
     *
     * @return void
     * @author suzuki_mar
     */
    protected function _setLayout()
    {
        $moduleName = $this->_getParam('module', 'default');

        // レイアウトの適用がうまくできないので、initメソッド内で設定する
        $options = array('layout' => 'layout',
            'layoutPath' => APPLICATION_PATH . "/modules/{$moduleName}/views/layouts",
            'content' => 'content');
        Zend_Layout::startMvc($options);
    }

}
