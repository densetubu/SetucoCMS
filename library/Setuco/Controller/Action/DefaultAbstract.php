<?php
/**
 * defaultモジュールの共通のコントローラーです
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @category    Setuco
 * @package     Setuco
 * @subpackage  Controller_Action
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_DefaultAbstract extends Setuco_Controller_Action_Abstract
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
    
    /**
     * defaultモジュール共通でviewに変数を渡す処理をします。
     * 
     * @return void
     * @author suzuki-mar
     */
    public function postDispatch()
    {
    	
    	//tagテーブルのモデルクラスのインスタンス生成
    	$modelTag = new Default_Model_Tag();
    	//タグクラウドをviewにセットする
    	$this->view->tagCloud = $modelTag->getTagCloud();
    	
    	
    	//categoryテーブルのモデルクラスのインスタンス生成
    	$modelCategory = new Default_Model_Category();
    	//カテゴリー一覧をviewにセットする
    	$this->view->categoryList = $modelCategory->getCategoryList();
    	
    }
}
