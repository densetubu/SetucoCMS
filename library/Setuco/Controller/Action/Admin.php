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
     * 一覧ページで、1ページあたり何件のデータを表示するか
     */
    const PAGE_LIMIT = 10;


    /**
     * モジュール間の共通の設定
     *
     * @return void
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

    /**
     * ページャーの設定をして、ビューで使用できるようにする
     *
     * @retun void
     * @author suzuki-mar
     */
    public function setPagerForView($max)
    {
        
        //共通のページャーの設定をする
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('common/pager.phtml');

        //ページャークラスを生成する
        $paginator = Zend_Paginator::factory($max);
        $paginator->setCurrentPageNumber($this->_getParam('page'))->setItemCountPerPage(self::PAGE_LIMIT);

        //viewでpaginationControlを使用しなくても、表示できるようにする
        $paginator->setView($this->view);

        $this->view->paginator = $paginator;
    }
}
