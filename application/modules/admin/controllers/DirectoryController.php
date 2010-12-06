<?php
/**
 * 管理側のサイト構造一覧のコントローラ
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
 * サイト構造一覧のコントローラ
 *
 * @package    Admin
 * @subpackage Controller
 * @author     charlesvineyard
 */
class Admin_DirectoryController extends Setuco_Controller_Action_AdminAbstract
{

    /**
     * サイト構造サービス
     *
     * @var Admin_Model_Directory
     */
    private $_directory;

    /**
     * 初期処理
     *
     * @author charlesvineyard
     */
    public function init()
    {
        parent::init();
        $this->_directory = new Admin_Model_Directory();
    }

    /**
     * サイト構造(ディレクトリー)の一覧を表示するアクションです。
     *
     * @return void
     * @author charlesvineyard
     */
    public function indexAction()
    {
        $this->view->directory = $this->_directory->createDirectoryInfo();
    }

}
