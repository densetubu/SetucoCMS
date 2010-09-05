<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * オートローダーの初期化
     * 
     * @return void
     * @author Yuu Yamanaka
     */
    protected function _initAutoloader()
    {
        // せつこライブラリ有効
        Zend_Loader_Autoloader::getInstance()->registerNamespace('Setuco_');        
    }

    /**
     * 翻訳設定の初期化
     * 
     * @return void
     * @author Yuu Yamanaka
     */
    protected function _initTranslator()
    {
        $translator = new Zend_Translate(
            'array',
            realpath(APPLICATION_PATH . '/languages'),
            'ja',
            array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        
        Zend_Validate_Abstract::setDefaultTranslator($translator);
    }
    
    protected function _initPlugin()
    {
        $fc = Setuco_Application_BootstrapUtil::extractResource($this, 'FrontController');
        $fc->registerPlugin(new Setuco_Controller_Plugin());
    }
    
    /**
     * ビューを初期化
     * 
     * @return Zend_View
     * @author charlesvineyard
     */
    protected function _initView()
    {
        $view = new Zend_View();
        Zend_Dojo::enableView($view);
        $view->dojo()->enable()
                     ->setCdnBase(Zend_Dojo::CDN_BASE_GOOGLE)
                     ->addStyleSheetModule('dijit.themes.soria');

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        return $view;
    }
    
}
