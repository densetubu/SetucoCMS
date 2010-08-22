<?php
/**
 * 管理側のサイト情報編集のコントローラーです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category Setuco
 * @package Admin
 * @subpackage Controller
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     
 */


/**
 * @category Setuco
 * @package  Admin
 * @subpackage Controller
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author 
 */
class Admin_SiteController extends Setuco_Controller_Action_Admin
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
     * 更新処理のアクションです。
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this-_redirect('/admin/site/index');
    }

}



