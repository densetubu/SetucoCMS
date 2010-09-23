<?php
/**
 * 管理側のサイト情報編集のコントローラーです。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category 	Setuco
 * @package 	Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author  ece_m   
 */


/**
 * @category    Setuco
 * @package     Admin
 * @subpackage  Controller
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author 
 */
class Admin_SiteController extends Setuco_Controller_Action_Admin_Abstract
{


    /** 
     * サイト情報を表示するアクションです
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function indexAction()
    {   
		$service = new Admin_Model_Site();
		$this->view->sites = $service->getSiteInfo();
		
		
		
		$form = $this->_createForm();
		$form->setDefaults($service->getSiteInfo());
		$form->setDecorators( array(
    'FormElements',
    array('HtmlTag', array('tag' => 'dl')),
    'Form',
));
		
		$this->view->form = $form;
		
    }

    /**
     * サイト情報の更新処理のアクションです。
     * indexアクションに遷移します
     *
     * @return void
     * @author 
     * @todo 内容の実装 現在はスケルトン
     */
    public function updateAction()
    {
        $this-_redirect('/admin/site/index');
    }

    
   /**
    * フォームの雛形を作成します。
    * 
    * @return Zend_Form
    */
   private function _createForm()
   {
       $form = new Zend_Form();
       $form->setMethod('post');
       $form->addElement('text', 'name', array(
           'label'    => 'サイト名',
           'required' => true,
           'filters'  => array('StringTrim'),
       	   'class'	  => "tejkmnpijnpinomnpoijmopst"
       ));
       $form->addElement('text', 'url', array(
           'label'    => 'サイトURL',
           'required' => true,
           'filters'  => array('StringTrim'),
       ));
       $form->addElement('text', 'comment', array(
           'label'    => '説明',
           'required' => true,
           'filters'  => array('StringTrim'),
       ));
       $form->addElement('text', 'keyword', array(
           'label'    => 'キーワード',
           'required' => true,
           'filters'  => array('StringTrim'),
       ));

       
       
       
       // hiddenとボタン系のデコレータは必要最低限にする
       $form->setElementDecorators(array('ViewHelper'), array('id', 'submit'));
       
       return $form;
   }
    
}



