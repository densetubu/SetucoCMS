<?php
/**
 * 管理側のページを管理するコントローラー。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category 	Setuco
 * @package 	Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author	    akitsukaa     
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author	    akitsukaa 
 */
class Admin_PageController extends Setuco_Controller_Action_Admin_Abstract
{
    /** 
     * ページの一覧表示のアクション
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {

    }

    /** 
     * ページ新規作成フォームのアクション
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function formAction()
    {
    }
    
    /** 
     * ページを新規作成する
     * indexアクションに遷移します
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function createAction()
    {
        $this->_redirect('/admin/page/form');        
    }

    /** 
     * 作成したページを公開前にプレビューするアクション
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function previewAction()
    {
    }

    /** 
     * ページを更新処理するアクション
     * indexアクションに遷移します ※
     * ※ただしスケルトンのときだけ
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this->_redirect('/admin/page/index');        
    }

    /** 
     * ページを削除するアクション
     * indexアクションに遷移します
     *
     * @return void
     * @author	akitsukaa 
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/page/index');        
    }

}

