<?php
/**
 * タグ管理のコントローラ
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Admin
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     charlesvineyard
 */

/**
 * タグ管理のコントローラ
 *
 * @package    Admin
 * @subpackage Controller
 * @author     charlesvineyard
 */
class Admin_TagController extends Setuco_Controller_Action_AdminAbstract
{
    /** 
     * タグの新規作成するフォーム
     * タグの一覧表示のアクションです
     *
     * @return void
     * @author 
     * @todo モデルからデータを取得する
     */
    public function indexAction()
    {
        $service = new Admin_Model_Tag();

		$this->view->tags = $service->getTags();
		
		//ページャーの設定をする
        $this->setPagerForView(50);

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

