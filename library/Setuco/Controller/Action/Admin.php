<?php
/**
 * adminモジュールの共通のコントローラーです
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
abstract class Setuco_Controller_Action_Admin extends Setuco_Controller_Action_Abstract
{
    /**
     * ナビゲーションの設定情報
     * 
     * @var Zend_Navigation
     */    
    protected $_navigation;
    
    /**
     * モジュール間の共通の設定
     *
     * @return void
     * @author suzuki-mar charlesvineyard
     */
    public function init()
    {   
        //親クラスのメソッドを実行する
        parent::init();

        //モジュール間の共通レイアウトの設定
        $layout = $this->_helper->layout();
        $layout->setLayoutPath($this->_getModulePath() . 'views/layouts/');
        $layout->setLayout('layout');

        // ナビゲーションの設定
        $this->_navigation = $this->_initNavigation();
    }

    /**
     * ナビゲーションの設定情報を初期化します。
     * 
     * @return Zend_Navigation
     * @author charlesvineyard
     */
    protected function _initNavigation()
    {
        $navigationConfig = new Zend_Config_Xml($this->_getModulePath() . 'configs/navigation.xml', 'nav', true);
        $this->_defineNavigationId($navigationConfig);
        return new Zend_Navigation($navigationConfig);
    } 
    
    /**
     * コンフィグXMLのタグ名をそのコンテンツのIDとして設定します。
     * 
     * @param $config ナビゲーションコンフィグ
     * @return void
     * @author charlesvineyard
     */ 
    private function _defineNavigationId($config)
    {
        foreach ($config as $k => $v) {
            $v->id = $k;
            if (isset($v->pages)) {
                $this->_defineNavigationId($v->pages);
            }
        }
    }
    
    /**
     * アクションメソッドが呼ばれるの直前の処理です。
     * 
     * ビューにタイトルを設定します。
     * 
     * @return void
     * @author charlesvineyard
     */
    public function preDispatch()
    {
        $this->view->headTitle($this->_chooseHeadTitle());
    }
    
    /**
     * リクエストが来たページのタイトルを取得します。
     * 
     * ここでのタイトルはナビゲーションコンフィグにコントローラ、アクション、
     * タイトルが設定されているもののみが取得できます。
     * 
     * @return string|null  設定が見つかればタイトルを返し、見つからなければnullを返します。
     */
    protected function _chooseHeadTitle() {
        $currentNavController = $this->_navigation->findByController($this->getRequest()->getControllerName());
        
        // コントローラ名が設定に入っていなかったら
        if (! $currentNavController) {
            return null;
        }
        $currentNavAction = $currentNavController->findByAction($this->getRequest()->getActionName());
        
        // アクション名が設定に入っていなかったら
        if (! $currentNavAction) {
            return null;
        }
        return $currentNavAction->getTitle();
    }

}
