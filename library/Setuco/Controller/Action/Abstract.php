<?php

/**
 * SetucoCMSの最基底コントローラークラスです
 * Zend_Controller_Actionを継承しています
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @ve/**
  rsion
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @package    Setuco
 * @subpackage Controller_Action
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_Abstract extends Zend_Controller_Action
{

    /**
     * 一覧ページで、1ページあたり何件のデータを表示するか
     * @var null
     */
    protected $_pageLimit = null;

    /**
     * RESTリダイレクトのときにパラメーターのドットに付加する文字列
     *
     * @var string
     */
    const DOT_ADDITIONAL_STRING = '%^';

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
        $redirectParams = $this->_loadRedirectParams();
        $this->_restRedirectIfNeeded($redirectParams);

        if ($this->_isHTMLRequest()) {
            $this->_initLayout();
        }
        

        $this->view->addScriptPath($this->_getModulePath() . 'views/partials');
    }

    /**
     * REST形式にリダイレクトするパラメーターを取得する
     * ファイル名は rest-params.xml
     *
     * @return array REST形式にリダイレクトするパラメーター
     * @author suzuki-mar
     */
    protected function _loadRedirectParams()
    {

        //restリダイレクトしないモジュールはファイルが存在しない
        if (!file_exists($this->_getModulePath()
                        . 'configs/rest-params.xml')) {
            return null;
        }

        $restUrlConfig = new Zend_Config_Xml($this->_getModulePath()
                        . 'configs/rest-params.xml');
        $redirectParams = $restUrlConfig->toArray();

        // queryの属性の個数
        $queryParamCount = 2;

        //ひとつしか query がない場合、階層がずれるので調整する
        foreach ($redirectParams as $controller => $controllerParams) {
            foreach ($controllerParams as $action => $actionParams) {
                foreach ($actionParams as $query => $params) {
                    if (count($params) == $queryParamCount) {
                        $redirectParams[$controller][$action][$query] = array($params);
                    }
                }
            }
        }
        return $redirectParams;
    }

    /**
     * REST形式のURLにリダイレクトするものだったら、リダイレクトする
     *
     *
     * @param array $restParamConfigs リダイレクトするパラメーター配列
     * @return mixed  リダイレクトする場合はvoid しない場合はfalse
     * @author suzuki-mar
     */
    protected function _restRedirectIfNeeded($restParamConfigs)
    {
        if (!$this->_isRedirectNeeded($restParamConfigs)) {
            return false;
        }

        $controller = $this->_getParam('controller');
        $action     = $this->_getParam('action');
        $queryConfigs = $restParamConfigs[$controller][$action]['query'];

        //urlに付加するパラメーターのキーバリューを取得する
        $queryParams = array();
        foreach ($queryConfigs as $queryConfig) {
            $param = $this->_getParam($queryConfig['value']);
            if (is_array($param)) {
                foreach ($param as $index => $partialParam) {
                    $param[$index] = $this->_encodeRestParam($partialParam);
                }
            } else {
                $param = $this->_encodeRestParam($param);
            }
            $queryParams[$queryConfig['value']] = $param;
        }

        return $this->_helper->redirector(
                $action,
                $controller,
                $this->_getParam('module'),
                $queryParams);

    }

    /**
     * REST形式のURLにリダイレクトするかどうか判断します。
     *
     * @param array $redirectParams リダイレクトするパラメーター配列
     * @return bool リダイレクトする場合は true。しない場合はfalse。
     * @author suzuki_mar charlesvineyard
     */
    private function _isRedirectNeeded($restParamConfigs)
    {
        //設定ファイルに書いてあるものしか、リダイレクトしない
        //リダイレクトしないモジュールは、nullが渡ってくる
        if (is_null($restParamConfigs)) {
            return false;
        }

        if (!isset($_SERVER['QUERY_STRING']) || is_null($_SERVER['QUERY_STRING'])) {
            return false;
        }

        //複数選択の[]の部分がURLエンコードしている
        $queryString = urldecode($_SERVER['QUERY_STRING']);

        $controller = $this->_getParam('controller');
        $action     = $this->_getParam('action');

        if (!isset($restParamConfigs[$controller][$action]['query'])) {
            return false;
        }

        $queryConfigs = $restParamConfigs[$controller][$action]['query'];
        foreach ($queryConfigs as $index => $queryConfig) {
            //複数選択するものに対応するため
            if (is_array($this->_getParam($queryConfig['value']))) {
                $queryConfig['value'] .= "[]";
            }
            //必須クエリが queryString に入ってなかったらリダイレクトしない
            if ($queryConfig['required'] === 'true') {
                if (strpos($queryString, "{$queryConfig['value']}=") === false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * パラメーターをリダイレクトパラムで確実に送信出来るようにエンコードします。
     *
     * @param  string $param エンコードするパラメーター
     * @return string エンコード後のパラメーター
     * @author charlesvineyard
     */
    private function _encodeRestParam($param) {
        $result = $param;

        // '.'だけだと消える可能性があるので文字を付加する
        if ($param === '.') {
            $result = $param . Setuco_Controller_Action_Abstract::DOT_ADDITIONAL_STRING;
        }

        // '/'が入ってるとエラーになるのでエンコードする
        $result = urlencode($result);

        return $result;
    }

    /**
     * レイアウトを設定します。
     *
     * レイアウトをオフにするにはsetLayoutを実行しないようにする必要がある
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
     * HTMLのリクエストか
     *
     * @return boolean HTMLのリクエストか
     * @author suzuki_mar
     */
     protected function _isHTMLRequest()
     {
        if ($this->getRequest()->isXmlHttpRequest()) {
            return false;
        }

        if ($this->getRequest()->getParam('format') === 'json') {
            return false;
        }

        if ($this->getRequest()->getParam('format') === 'xml') {
            return false;
        }

        return true;

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
}
