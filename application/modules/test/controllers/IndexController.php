<?php

/**
 * 開発のサポートをするコントローラー
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Install
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 * 
 */

/**
 * Description of InstallController
 *
 * @author suzuki-mar
 */
class Test_IndexController
    extends Setuco_Controller_Action_Abstract
{

    /**
     * アクションの共通設定
     * @author suzuki-mar
     */
    function init()
    {
        parent::init();
    }

    /**
     * リンク画面
     *
     * @author suzuki-mar
     * @return void
     */
    public function indexAction()
    {

    }

    /**
     * テストデータを入れる画面
     *
     * @author suzuki-mar
     */
    public function insertTestDataAction()
    {
        Setuco_Test_Util::initDb('development');
        Setuco_Test_Util::initFile();
    }

    /**
     * DBを初期化する
     *
     * @author suzuki-mar
     */
    public function initializationDbAction()
    {
        $dbInit = new Test_Model_DbInitialization();
        $dbInit->dropAllTables();
        $dbInit->initializeDb();
    }

}