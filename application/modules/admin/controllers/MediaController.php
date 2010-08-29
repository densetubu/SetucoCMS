<?php
/**
 * ファイル管理のコントローラ
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
 * @author     akitsukada
 */


/**
 * ファイル管理画面の操作を行うコントローラ
 * 
 * @package    Admin
 * @subpackage Controller
 * @author     akitsukada
 */
class Admin_MediaController extends Setuco_Controller_Action_Admin
{
    /** 
     *
     * ファイルのアップロードフォームや
     * アップロードしてあるファイルの一覧を表示するアクションです
     *
     * @return void
     * @author akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {

        /* リクエスト取得 */
        $req = $this->getRequest();

        /* ファイルのデータ取得 */
        // 絞込条件の有無
        $fileType = $req->getUserParam('fileType');
        $media = new Admin_Model_Media();
        if ($fileType !== null) {

        } else {
        }
         

        // paginator 設定
        $currentPage = 1;
        if ($req->getUserParam('page') !== null && is_numeric($req->getUserParam('page'))) {
            $currentPage = $req->getUserParam('page');
        }
        echo $currentPage;

    }

    /** 
     * ファイルのアップロード処理のアクションです
     * indexアクションに遷移します
     *
     * @return void
     * @author akitsukada
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
     * @author akitsukada
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
     * @author akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function deleteAction()
    {
        $this->_redirect('/admin/media/index');        
    }

    /** 
     * アップロード済みのファイルを編集するアクションです
     *
     * @return void
     * @author akitsukada
     * @todo 内容の実装 現在はスケルトン
     */
    public function formAction()
    {

    }
}
