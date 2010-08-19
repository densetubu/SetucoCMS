<?php

class Admin_SiteController extends Zend_Controller_Action
{


    public function indexAction()
    {
        
    }
    
    public function updateAction()
    {
        $this->_redirect('/admin/site/index');        
    }

}

