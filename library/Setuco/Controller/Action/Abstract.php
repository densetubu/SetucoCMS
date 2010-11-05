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
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('common/pager.phtml');

        //現在のページ番号を取得する
        $page = $this->_getPageNumber();

        //現在のページ番号を渡す
        $this->view->page = $page;

        //ページャークラスを生成する
        $paginator = Zend_Paginator::factory($max);
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($limit);

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
     * ページネーターで使う現在の（クリックされた）ページ番号を取得するメソッドです　削除するので使用しないで、_getPageNumberメソッドを使用してください
     * 古いメソッド名　遅くても11月には廃止予定
     *
     * @return int 現在ページネーターで表示すべきページ番号
     * @author suzuki-mar
     *
     * @todo メソッドの削除
     */
    protected function _getPage()
    {
        $result = $this->_getPageNumber();
        return $result;
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
    protected function _setPageLimit($limitPage)
    {
        $this->_pageLimit = $limitPage;
        return $result;
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
