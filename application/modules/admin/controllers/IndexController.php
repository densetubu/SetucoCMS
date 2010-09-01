<?php
/**
 * 管理側のTOPページのコントローラーです。
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
class Admin_IndexController extends Setuco_Controller_Action_Admin
{
    /** 
     * トップページのアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {
    }

    /** 
     * 更新処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */ 
    public function updateAmbitionAction()
    {
        $this->_redirect('/admin/index/index');
    }

    /** 
     * 野望を更新するフォームを表示するアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function formGoalAction()
    {
        $flashMessages = $this->_helper->flashMessenger->getMessages();
        if (count($flashMessages)) {
            $this->view->flashMessage = $flashMessages[0];
        }
    }

    /** 
     * 野望を更新するアクションです 
     * formGoalアクションに遷移します 
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateGoalAction()
    {
        $this->_helper->flashMessenger('更新目標を変更しました。');
        $this->_redirect('/admin/index/form-goal');
    }

}

