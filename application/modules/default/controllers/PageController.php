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
 * @author      suzuki-mar    
 */



/**
 * @category    Setuco
 * @package     Default
 * @subpackage  Controller
 * @author      suzuki-mar
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
     * @author 
     * @todo 内容を実装する　現在はスケルトン
     */
    public function indexAction()
    {

    }

    /**
     * 検索結果を表示するアクションです
     * リダイレクトを確認するために実装したので、デバッグ表示してexitしている
     * 
     * @return void
     * @todo 
     */
    public function searchAction()
    {
        
        
        
    }

}
