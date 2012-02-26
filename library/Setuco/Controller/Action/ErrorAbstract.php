<?php
/**
 * エラーコントローラーの継承クラス
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
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @package    Setuco
 * @subpackage Controller_Action
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_ErrorAbstract extends Setuco_Controller_Action_Abstract
{
    /**
     * HTTPステータスコードを 404(Not Found) とするエラーハンドラーのタイプの配列
     *
     * @var array
     */
    private $_notFoundErrorHandlerTypes = array (
        Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE,
        Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER,
        Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION,
    );

    /**
     * レイアウトを設定します。
     *
     * @return void
     * @author charlesvineyard
     */
    protected function _initLayout()
    {
        $this->_helper->layout->disableLayout();
    }

    /**
     * 運用時の処理です。
     *
     * @return void
     * @author charlesvineyard
     */
    protected function _productionOperation()
    {
        $code = $this->_setHttpResponseCode();

        $viewFile = 'error-default';
        if (404 == $code) {
            $viewFile = 'error-404';
        }
        $this->_helper->viewRenderer->setScriptAction($viewFile);
    }

    /**
     * HTTPレスポンスコード(ステータスコード)を設定します。
     *
     * @return int 設定したコード
     * @author charlesvineyard
     */
    protected function _setHttpResponseCode()
    {
        $code = 500;
        if ($this->_is404Error()) {
            $code = 404;
        }
        $this->getResponse()->setHttpResponseCode((int) $code);
        return $code;
    }

    /**
     * HTTPステータスコードを 404(Not Found) とするか判断します。
     *
     * @return bool するなら true。しないなら false。
     * @author charlesvineyard
     */
    protected function _is404Error()
    {
        $errorHandler = $this->_getParam('error_handler');
        if (!is_null($errorHandler)) {
            // エラーハンドラータイプが NotFound にするものだったら
            if (array_search($errorHandler->type,
                $this->_notFoundErrorHandlerTypes) !== false) {
                return true;
            }
        }

        if ($this->getResponse()->isException()) {
            $exceptions = $this->getResponse()->getException();
            if (404 == $exceptions[0]->getCode()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 開発時の処理です。
     *
     * @return void
     * @author charlesvineyard
     */
    protected function _developmentOperation()
    {
        $this->_setHttpResponseCode();
        $this->view->message = $this->_getMessage();

        $errorHandler = $this->_getParam('error_handler');
        $requestParams = $errorHandler->request->getParams();
        $this->_replaceInvisibleParam(array('password'), $requestParams);
        $this->view->requestParams = $requestParams;

        //ログが有効になっている場合は、ログの例外メッセージをセットする
        $log = $this->_getErrorLog();
        if ($log !== false) {
            $log->crit($this->view->message, $errorHandler->exception);
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errorHandler->exception;
        }
    }

    /**
     * メッセージを取得します。
     *
     * @return string メッセージ
     * @author charlesvineyard
     */
    protected function _getMessage()
    {
        if ($this->_is404Error()) {
            return 'Page not found';
        }
        return 'Application error';
    }

    /**
     * 不可視のパラメータ(パスワードなど)を伏せ字に置換します。
     *
     * @param array $invisibleParamNames 不可視の置換したいパラメータのキーの配列
     * @param array $params キー:パラメータ名、値:パラメータ値の連想配列
     */
    protected function _replaceInvisibleParam($invisibleParamNames, &$params)
    {
        $replaceChar = '*';
        foreach((array) $invisibleParamNames as $paramName) {
            if (!array_key_exists($paramName, $params)) {
                continue;
            }
            $replacement = '';
            for ($i = 0; $i < strlen($params[$paramName]); $i++) {
                $replacement .= $replaceChar;
            }
            $params[$paramName] = $replacement;
        }
    }

    /**
     * ログのプラグインリソースが有効になっているときに取得する
     *
     * @return Logのリソース 有効でない場合はfalse
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
}
