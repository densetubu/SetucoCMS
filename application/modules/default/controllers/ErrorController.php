<?php
/**
 * 閲覧側のエラーコントローラー
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
 * @package    Controller
 * @subpackage Controller
 * @author     suzuki-mar
 */
class ErrorController extends Setuco_Controller_Action_DefaultAbstract
{
    /**
     * クラスの共通設定 
     *
     */
    public function init()
    {
        //モジュール毎に違うレイアウトを表示する
        $this->_setLayout();

    }

    public function errorAction()
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

        $this->view->request   = $errors->request;
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

