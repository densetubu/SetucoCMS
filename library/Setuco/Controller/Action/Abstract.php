<?php
/**
 * SetucoCMSの最基底コントローラークラスです
 * Zend_Controller_Actionを継承しています
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @category    Setuco
 * @package     Setuco_Controller
 * @subpackage  Action
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_Abstract extends Zend_Controller_Action  
{
    /**
     * 全てのコントローラ共通の初期処理です。
     *
     * @return void
     * @author suzuki-mar charlesvineyard
     */
    public function init()
    {   
        parent::init();
        $this->_initLayout();
    }   

    /**
     * レイアウトを設定します。
     * 
     * @return void
     * @author suzuki_mar charlesvineyard
     */
    protected function _initLayout()
    {
        $layout = $this->_helper->layout();
        $layout->setLayoutPath($this->_getModulePath() . 'views/layouts/');
        $layout->setLayout($this->getLayoutName());
    }
    
    /**
     * レイアウト名を取得します。
     * 
     * @return string レイアウト名
     * @author charlesvineyard
     */
    protected function getLayoutName()
    {
        return 'layout';
    }

    /**
     * モジュールのディレクトリーのパスを取得する
     *
     * @return String モジュールのディレクトリーのパス
     * @author suzuki_mar
     */
    protected function _getModulePath()
    {   
        $result = APPLICATION_PATH . "/modules/{$this->_getParam('module')}/";
        return $result;
    }  
}
