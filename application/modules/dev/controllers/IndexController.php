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
class Dev_IndexController
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

    public function initializationAction()
    {
        Setuco_Test_Util::initDb();
        Setuco_Test_Util::initFile();
    }

}
