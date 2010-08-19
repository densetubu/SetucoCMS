<?php

class Admin_TagController extends Zend_Controller_Action
{

    public function indexAction()
    {
        
    }
    
    public function createAction()
    {
        $this->_redirect('/admin/tag/index');        
    }

    public function updateAction()
    {
        $this->_redirect('/admin/tag/index');        
    }

    public function deleteAction()
    {
        $this->_redirect('/admin/tag/index');        
    }

}

