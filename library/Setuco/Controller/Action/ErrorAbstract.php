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
     * HTTPレスポンスコード(ステータスコード)を設定します。
     *
     * @return int 設定したコード
     * @author charlesvineyard
     */
    protected function _setHttpResponseCode() {
        $code = 500;
        if ($this->_is404Error()) {
            $code = 404;
        }
        $this->getResponse()->setHttpResponseCode((int) $code);
        return $code;
    }

    /**
     * 運用時の処理です。
     *
     * @return void
     * @author charlesvineyard
     */
    protected function _productionOperation() {
        $code = $this->_setHttpResponseCode();

        $viewFile = 'error-default';
        if (404 == $code) {
            $viewFile = 'error-404';
        }
        $this->_helper->viewRenderer->setScriptAction($viewFile);
    }

    /**
     * 開発時の処理です。
     *
     * @return void
     * @author charlesvineyard
     */
    protected function _developmentOperation() {
        $this->_setHttpResponseCode();
        $this->view->message = $this->_getMessage();

        $errorHandler = $this->_getParam('error_handler');
        $this->view->request = $errorHandler->request;

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
     * HTTPステータスコードを 404(Not Found) とするか判断します。
     *
     * @return bool するなら true。しないなら false。
     * @author charlesvineyard
     */
    protected function _is404Error() {
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
     * メッセージを取得します。
     *
     * @return string メッセージ
     * @author charlesvineyard
     */
    protected function _getMessage() {
        if ($this->_is404Error()) {
            return 'Page not found';
        }
        return 'Application error';
    }
}
