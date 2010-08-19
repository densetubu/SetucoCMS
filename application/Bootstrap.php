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
}
