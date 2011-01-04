<?php

/**
 * 管理側のエラーコントローラー
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link       
 * @version    
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * エラーコントローラー
 * 
 * @package    Admin
 * @subpackage Controller
 * @author     suzuki-mar
 */
class Admin_ErrorController extends Setuco_Controller_Action_AdminAbstract
{

    /**
     * クラスの共通設定 
     *
     */
    public function init()
    {
        //親コントローラーの設定を引き継ぐ
        parent::init();

        //モジュール毎に違うレイアウトを表示する
        $this->_setLayout();
    }

    public function errorAction()
    {
        $this->_originalErrorAction();

        //開発時はデフォルトのエラービューを表示する
        if (APPLICATION_ENV === 'production') {
            $this->_changeErrorRender();
        }
    }

    /**
     * スクリプト(view)ファイルを、本番環境ように変更する
     *
     * @author suzuki-mar
     */
    protected function _changeErrorRender()
    {
        $errors    = $this->_getParam('error_handler');
        $errorCode = $this->_getParam('errorCode', $this->_getResponeByErrorType($errors->type));

        switch ($errorCode) {
            case '404':
                $viewFile = 'error_404';
                break;

            default:
                $viewFile = 'error_default';
                break;
        }

        $this->_helper->viewRenderer->setScriptAction($viewFile);
    }

    /**
     * エラータイプ(error_handelr->type)からレスポンスコードを求める
     *
     * @param string $errorType error_handelr->type
     * @return int レスポンスコード
     * @author suzuki-mar
     */
    protected function _getResponeByErrorType($errorType)
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
     * オリジナルのエラーアクション処理
     * 新しくロジックを書くところと分けるために定義する
     * ZFにはじめから定義してあったロジック
     *
     * @return void
     * @author suzuki-mar
     */
    protected function _originalErrorAction()
    {

        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    public function getLog()
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
     * エラーコントローラーなど、レイアウトが無効になっているものに使用する
     *
     * @return void
     * @author suzuki_mar
     */
    private function _setLayout()
    {
        $moduleName = $this->_getParam('module', 'default');

        // レイアウトの適用がうまくできないので、initメソッド内で設定する
        $options = array('layout' => 'layout',
            'layoutPath' => APPLICATION_PATH . "/modules/{$moduleName}/views/layouts",
            'content' => 'content');
        Zend_Layout::startMvc($options);
    }

}

