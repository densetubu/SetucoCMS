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



	/**
	 * 使用するプラグインを登録する
	 *
	 * @return void
	 * @author suzuki_mar
	 */
	protected function _initPlugin()
	{
		//プラグインを登録するために、フロントコントローラーのインスタンを取得する
		$frontController = Setuco_Application_BootstrapUtil::extractResource($this, 'FrontController');

		//基本的なコントローラーに関するプラグインを登録する
		$frontController->registerPlugin(new Setuco_Controller_Plugin());
		//エラーコントローラーを制御するプラグインを登録する
		$frontController->registerPlugin(new Setuco_Controller_Plugin_ErrorHandler());

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
