<?php
/**
 * adminモジュールの共通のコントローラーです
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */



/**
 * @category    Setuco
 * @package     Setuco_Controller
 * @subpackage  Action
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_Admin extends Setuco_Controller_Action_Abstract
{
    /**
     * モジュール間の共通の設定
     *
     * @void
     * @author suzuki-mar
     */
    public function init()
    {   
        //親クラスのメソッドを実行する
        parent::init();

        //モジュール間の共通レイアウトの設定
        $layout = $this->_helper->layout();
        $layout->setLayoutPath($this->_getModulePath() . 'views/layouts/');
        $layout->setLayout('layout');

    }   

}
