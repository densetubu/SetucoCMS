<?php
/**
 * 管理側のサイト構造一覧のコントローラーです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category    Setuco
 * @package     Admin
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
class Admin_DirectoryController extends Setuco_Controller_Action_Admin
{
    /** 
     * サイト構造(ディレクトリー)の一覧を表示するのアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {
        $directory = new Admin_Model_Directory();
        $this->view->directory = $directory->load();
    }
}
