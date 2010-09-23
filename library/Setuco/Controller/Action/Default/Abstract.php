<?php
/**
 * defaultモジュールの共通のコントローラーです
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
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_Default_Abstract extends Setuco_Controller_Action_Abstract
{
    /**
     * defaultモジュールコントローラの初期処理です。
     *
     * @return void
     * @author suzuki-mar
     */
    public function init()
    {   
        parent::init();
    } 
}
