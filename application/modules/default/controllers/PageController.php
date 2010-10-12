<?php

/**
 * 閲覧側のページを表示するコントローラーです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category    Setuco
 * @package     Default
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki-mar akitsukada
 */



/**
 * @category    Setuco
 * @package     Default
 * @subpackage  Controller
 * @author      suzuki-mar akitsukada
 */
class PageController extends Setuco_Controller_Action_DefaultAbstract
{

    /** 
     * アクションの共通設定
     *
     * @return void
     * @author suzuki_mar
     */
    public function init()
    {   
        //モジュール間の共通の設定を実行
        parent::init();

    }

    /**
     * トップページのアクションです
     *
     * @return void
     * @author suzuki-mar
     * @todo 内容を実装する　現在はスケルトン
     */
    public function indexAction()
    {

    }

    /**
     * 検索結果を表示するアクションです
     * 
     * @return void
     * @author akitsukada
     * @todo 実装（現在スケルトン）
     */
    public function searchAction()
    {
        
        
    }

    /**
     * あるカテゴリーに属するページの一覧を表示する
     *
     * @return void
     * @author akitsukada
     * @todo 実装（現在スケルトン）
     */
    public function categoryAction()
    {

    }


    /**
     * あるタグがつけられたページの一覧を表示する
     *
     * @return void
     * @author akitsukada
     * @todo 実装（現在スケルトン）
     */
    public function tagAction()
    {

    }

    /**
     * ページを閲覧する
     *
     * @return void
     * @author akitsukada
     * @todo 実装（現在スケルトン）
     */
    public function showAction()
    {

    }

}
