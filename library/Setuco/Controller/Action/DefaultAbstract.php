<?php
/**
 * defaultモジュールの共通のコントローラーです
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
abstract class Setuco_Controller_Action_DefaultAbstract extends Setuco_Controller_Action_Abstract
{
	/**
	 * ページのタイトル
	 *
	 * @var String
	 */
	protected $_pageTitle = '';

	/**
	 * defaultモジュールコントローラの初期処理です。
	 *
	 * @return void
	 * @author suzuki-mar
	 */
	public function init()
	{
		parent::init();

		//REST形式のURLにリダイレクトする リダイレクトしないとREST形式のURLにならないので
		$redirectParams[] = array('module' => 'default', 'controller' => 'page', 'action' => 'search', 'params' => 'query');

		foreach ($redirectParams as $value) {
			if (preg_match("/^\/{$value['controller']}\/{$value['action']}/", $_SERVER['REQUEST_URI'])) {
				if (preg_match("/[?|&]{$value['params']}=/", $_SERVER['REQUEST_URI'])) {
					$query = $this->_getParam($value['params']);
					$this->_helper->redirector(
					   $value['action'], 
					   $value['controller'], 
					   $value['module'], 
					   array($value['params'] => $query));
				}
			}

		}

	}

	/**
	 * defaultモジュール共通でviewに変数を渡す処理をします。
	 *
	 * @return void
	 * @author suzuki-mar
	 */
	public function postDispatch()
	{
		//tagテーブルのモデルクラスのインスタンス生成
		$modelTag = new Default_Model_Tag();
		//タグクラウドをviewにセットする
		$this->view->tagCloud = $modelTag->getTagCloud();
		 
		//categoryテーブルのモデルクラスのインスタンス生成
		$modelCategory = new Default_Model_Category();
		//カテゴリー一覧をviewにセットする
		$this->view->categoryList = $modelCategory->getCategoryList();
		 
		//siteテーブルのモデルクラスのインスタンス生成
		$modelSite = new Default_Model_Site();
		//サイト情報をviewにセットする
		$this->view->siteInfos     = $modelSite->getSiteInfos();
		 
		//ページタイトルをセットする
		$this->view->pageTitle = $this->_pageTitle;
		 
	}
}
