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
 * @ve/**
  rsion
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
     * 一覧ページで、1ページあたり何件のデータを表示するか
     * @var null
     * @todo PAGE_LIMITの削除
     */
    protected $_pageLimit = null;

    /**
     * 全てのコントローラ共通の初期処理です。
     *
     * @return void
     * @author suzuki-mar charlesvineyard
     */
    public function init()
    {
        parent::init();

        //REST形式でリダイレクトするURLだったら、リダイレクトする
        // /page/search/query/test みたいなURLにリダイレクトする
        if ($this->_isRestRedirect($this->_getRedirectParams())) {
            $this->_restRedirect($this->_getRedirectParams());
        }


        $this->_initLayout();
        $this->view->addScriptPath($this->_getModulePath() . 'views/partials');
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
        $layout->setLayout('layout');
    }

    /**
     * レイアウト名を設定します。
     *
     * レイアウト名はレイアウトファイルの拡張子無しのファイル名です。
     *
     * @return void
     * @author charlesvineyard
     */
    protected function _setLayoutName($layoutName)
    {
        $this->_helper->layout()->setLayout($layoutName);
    }

    /**
     * モジュールのディレクトリーのパスを取得する
     *
     * @return String モジュールのディレクトリーのパス
     * @author suzuki_mar
     */
    protected function _getModulePath()
    {
        return APPLICATION_PATH . "/modules/{$this->_getParam('module')}/";
    }

    /**
     * ページャーの設定をして、ビューで使用できるようにする
     *
     * @param int 最大何件のデータが該当したのか
     * @param int[option] 一ページあたりに何件のデータを表示するのか
     * @return void
     * @author suzuki-mar
     */
    public function setPagerForView($max, $limit = null)
    {
        //数値ではない場合はfalseを返す (ありなえいので)
        if (!is_int($max)) {
            return false;
        }

        //指定がなければ、デフォルトを使用する
        if (is_null($limit)) {
            $limit = $this->_getPageLimit();
        }


        //共通のページャーの設定をする
        Zend_Paginator::setDefaultScrollingStyle('Jumping');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pager.phtml');

        //現在のページ番号を取得する
        $page = $this->_getPageNumber();

        //現在のページ番号を渡す
        $this->view->page = $page;

        //ページャークラスを生成する
        $paginator = Zend_Paginator::factory($max);
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($limit)
                ->setPageRange(5);

        //viewでpaginationControlを使用しなくても、表示できるようにする
        $paginator->setView($this->view);

        //ページャーをviewで使用できるようにする
        $this->view->paginator = $paginator;
    }

    /**
     * ページネーターで使う現在の（クリックされた）ページ番号を取得するメソッドです
     *
     * @return int 現在ページネーターで表示すべきページ番号
     * @author akitsukada suzuki-mar
     */
    protected function _getPageNumber()
    {
        // URLからページ番号の指定を得る ( デフォルトは1 )
        $currentPage = $this->_getParam('page');
        if (!is_numeric($currentPage)) {
            $currentPage = 1;
        }

        $currentPage = (int) $currentPage;
        return $currentPage;
    }

    /**
     * 一ページあたりの取得件数の_pageLimitのゲッター
     * @return int 一ページあたりの取得件数
     * @author suzuki-mar
     */
    protected function _getPageLimit()
    {
        $result = $this->_pageLimit;
        return $result;
    }

    /**
     * 一ページあたりの取得件数の_pageLimitのセッター
     * メソッドチェーンを使用できる
     *
     * @param int $limitPage 1ページあたりの取得件数
     * @return $this 自分自身のインスタンス
     * @author suzuki-mar
     */
    protected function _setPageLimit($pageLimit)
    {
        $this->_pageLimit = $pageLimit;
        return $this;
    }

    /**
     * フラッシュメッセージがアクションヘルパーに設定されていればビューにセットして可視化します。
     *
     * @param  string $paramName ビューにセットする変数名。デフォルトは "flashMessage"。
     * @return void
     * @author charlesvineyard
     */
    protected function _showFlashMessages($paramName = 'flashMessages')
    {
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->$paramName = $flashMessages;
        }
    }

    /**
     * REST形式にリダイレクトするパラメーターを取得する
     * module controller action parameter
     * ファイル名はrest_url.xml
     *
     * @return array REST形式にリダイレクトするパラメーター
     * @author suzuki-mar
     */
    protected function _getRedirectParams()
    {

        //restリダイレクトしないモジュールはファイルが存在しない
        if (!file_exists($this->_getModulePath()
                        . 'configs/rest-params.xml')) {
            return null;
        }

        $restUrlConfig = new Zend_Config_Xml($this->_getModulePath()
                        . 'configs/rest-params.xml');

        $redirectParams = $restUrlConfig->toArray();
        return $redirectParams;
    }

    /**
     * REST形式にリダイレクトするか
     * 同じactionにしかリダイレクトしない
     *
     * @param array $redirectParams REST形式にリダイレクトするデータ配列
     * @return boolean リダイレクトするか
     * @author suzuki-mar
     */
    protected function _isRestRedirect($redirectParams)
    {
        //リダイレクトしないモジュールは、nullが渡ってくる
        if (is_null($redirectParams)) {
            return false;
        }

        foreach ($redirectParams as $value) {
            //可読性を上げるために一時変数を作成する
            $controller = $this->_getParam('controller');
            $action = $this->_getParam('action');
            if (isset($redirectParams[$controller][$action])) {
                $query = $redirectParams[$controller][$action];

                if (strpos($_SERVER['QUERY_STRING'], "{$query}=") !== false) {
                    return true;
                }
            }
        }


        return false;
    }

    /**
     * REST形式のURLにリダイレクトする
     * 同じactionにしかリダイレクトしない
     *
     * @param array $redirectParams リダイレクトするパラメーター配列
     * @return void redirectするので redirectしないばあいはfalse
     * @author suzuki-mar
     */
    protected function _restRedirect($redirectParams)
    {
        //リダイレクトしないモジュールは、nullが渡ってくる
        if (is_null($redirectParams)) {
            return false;
        }


        //可読性を上げるためモジュール名などを一時引数にする
        extract($this->_getAllParams());

        //アクションまでは常に存在する
        //REST形式でリダイレクトするかチェックしていないかもしれないので
        if (!isset($redirectParams[$controller][$action])) {
            return false;
        }

        $queryName = $redirectParams[$controller][$action];
        $queryParam = $this->_getParam($queryName);

        return $this->_helper->redirector(
                $action,
                $controller,
                $module,
                array($queryName => $queryParam));
    }

}
