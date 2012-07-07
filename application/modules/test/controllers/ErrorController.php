<?php
/**
 * testモジュールのエラーコントローラー
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Install
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
 * @package    Install
 * @subpackage Controller
 * @author     suzuki-mar
 */
class Test_ErrorController extends Setuco_Controller_Action_ErrorAbstract
{
    /**
     * エラー画面を表示する
     *
     * @return void
     * @author suzuki-mar
     */
    public function errorAction()
    {

        // URL(admin/error/error)で直接アクセスの対策
        if (is_null($this->_getParam('error_handler'))) {
            throw new Setuco_Controller_IllegalAccessException('ページがありません。', 404);
        }

        if (APPLICATION_ENV === 'production') {
            $this->_productionOperation();
        } else if (APPLICATION_ENV === 'development') {
            $this->_developmentOperation();
        } else {
            $this->_developmentOperation();
        }
    }

}
