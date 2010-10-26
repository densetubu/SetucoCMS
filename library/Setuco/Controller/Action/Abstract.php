<?php
/**
 * SetucoCMSの最基底コントローラークラスです
 * Zend_Controller_Actionを継承しています
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
abstract class Setuco_Controller_Action_Abstract extends Zend_Controller_Action  
{
	
    /**
     * 全てのコントローラ共通の初期処理です。
     *
     * @return void
     * @author suzuki-mar charlesvineyard
     */
    public function init()
    {   
        parent::init();
        $this->_initLayout();
    }   

    /**
     * レイアウトを設定します。
     * 
     * @return void
     * @author suzuki_mar charlesvineyard
     */
    protected function _initLayout()
    {
        $layout = $this->_helper->layout();
        $layout->setLayoutPath($this->_getModulePath() . 'views/layouts/');
        $layout->setLayout($this->getLayoutName());
    }
    
    /**
     * レイアウト名を取得します。
     * 
     * @return string レイアウト名
     * @author charlesvineyard
     */
    protected function getLayoutName()
    {
        return 'layout';
    }

    /**
     * モジュールのディレクトリーのパスを取得する
     *
     * @return String モジュールのディレクトリーのパス
     * @author suzuki_mar
     */
    protected function _getModulePath()
    {   
        $result = APPLICATION_PATH . "/modules/{$this->_getParam('module')}/";
        return $result;
    }

    /**
     * ページャーの設定をして、ビューで使用できるようにする
     *
     * @param int 最大何件のデータが該当したのか
     * @param int[option] 一ページあたりに何件のデータを表示するのか
     * @return void
     * @author suzuki-mar
     */
    public function setPagerForView($max, $limit = null)
    {
        //数値ではない場合はfalseを返す (ありなえいので)
        if(!is_int($max)) {
            return false;
        }
        
        //指定がなければ、デフォルトを使用する
        if (is_null($limit)) {
            
        	//モジュールごとの取得するものを変更する
        	//モジュール名を大文字にする。定数の名前に合わせるために
        	$moduleConstName  = ucwords($this->_getParam('module')). "Abstract";
        	$limit = constant("Setuco_Controller_Action_{$moduleConstName}::PAGE_LIMIT");        	
        }
        

        //共通のページャーの設定をする
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('common/pager.phtml');

        //現在のページ番号を取得する
        $page = $this->_getPage();

        //現在のページ番号を渡す
        $this->view->page = $page;

        //ページャークラスを生成する
        $paginator = Zend_Paginator::factory($max);
        $paginator->setCurrentPageNumber($page)->setItemCountPerPage($limit);

        //viewでpaginationControlを使用しなくても、表示できるようにする
        $paginator->setView($this->view);

        //ページャーをviewで使用できるようにする
        $this->view->paginator = $paginator;

    }
    
    /**
     * ページネーターで使う現在の（クリックされた）ページ番号を取得するメソッドです
     *
     * @return int 現在ページネーターで表示すべきページ番号
     * @author akitsukada suzuki-mar
     */
    protected function _getPage()
    {
        // URLからページ番号の指定を得る ( デフォルトは1 )
        $currentPage = $this->_getParam('page');
        if (!is_numeric($currentPage)) {
            $currentPage = 1;
        }

        $currentPage = (int) $currentPage;
        return $currentPage;
    }
    
}
