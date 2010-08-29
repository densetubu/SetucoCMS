<?php
/**
 * 管理側のタグを管理するコントローラーです。
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

require_once '../application/modules/common/models/DbTable/Site.php';

/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author 
 */
class Admin_TagController extends Setuco_Controller_Action_Admin
{
    /** 
     * タグの新規作成するフォーム
     * タグの一覧表示のアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {
        echo '<pre>';

        $dbtable = new Common_Model_DbTable_Site();
        var_dump($dbtable->find('1')->toArray());

        echo '</pre>';

        exit;  



    }

    /** 
     * タグを新規作成するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function createAction()
    {
        $this->_redirect('/admin/tag/index');        
    }

    /** 
     * タグを更新処理するアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this->_redirect('/admin/tag/index');        
    }

    /** 
     * タグを削除するアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/tag/index');        
    }

}

