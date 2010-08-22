<?php
/**
 * 管理側のアップロードしたファイルを管理するのコントローラーです。
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
 * @author     
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author 
 */
class Admin_MediaController extends Setuco_Controller_Action_Admin
{
    /** 
     *
     * ファイルのアップロードフォーム
     * アップロードしてあるファイルの一覧を表示するアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {

    }

    /** 
     * ファイルのアップロード処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function createAction()
    {
        $this->_redirect('/admin/media/index');        
    }

    /** 
     * 更新処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this->_redirect('/admin/media/index');        
    }

    /** 
     * 削除処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/media/index');        
    }

}
