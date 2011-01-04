<?php

/**
 * 管理側のエラーコントローラー
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
 * @author     suzuki-mar
 */

/**
 * エラーコントローラー
 * 
 * @package    Admin
 * @subpackage Controller
 * @author     suzuki-mar
 */
class Admin_ErrorController extends Setuco_Controller_Action_AdminAbstract
{

    /**
     * クラスの共通設定 
     *
     */
    public function init()
    {
        //親コントローラーの設定を引き継ぐ
        parent::init();

        //モジュール毎に違うレイアウトを表示する
        $this->_setLayout();
    }

    /**
     * エラー画面を表示する
     *
     * @return void
     * @author suzuki-mar
     */
    public function errorAction()
    {
        $this->_setErrorResponeCode();

        //本番時は別のエラーファイルを表示する
        if (APPLICATION_ENV === 'production') {
            $this->_changeErrorRender();
        //開発時はデフォルトのエラービューを表示する
        } else {
            $this->_setDefaultErrorMessageForView();
        }
    }

}

