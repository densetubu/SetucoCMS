<?php
/**
 * 管理側のカテゴリーページのコントローラーです。
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
 * @author      charlesvineyard
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      charlesvineyard
 */
class Admin_CategoryController extends Setuco_Controller_Action_Admin
{
    /** 
     * カテゴリーの新規作成するフォーム
     * カテゴリーの一覧表示のアクションです
     *
     * @return void
     * @author charlesvineyard
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {
        //ページャーの設定をする
        $this->setPagerForView(50);

    }


    /** 
     * カテゴリーを新規作成するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     * @todo 内容の実装 現在はスケルトン
     */
    public function createAction()
    {
        $this->_redirect('/admin/category/index');        
    }

    /** 
     * カテゴリーを更新処理するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author charlesvineyard
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this->_redirect('/admin/category/index');        
    }

    /** 
     * カテゴリーを削除するアクションです
     *
     * @return void
     * @author charlesvineyard
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/category/index');        
    }

}

