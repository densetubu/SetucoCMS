<?php

class Admin_MediaController extends Zend_Controller_Action
{

    public function indexAction()
    {
        
    }
    
    public function createAction()
    {
        $this->_redirect('/admin/media/index');        
    }

    public function updateAction()
    {
        $this->_redirect('/admin/media/index');        
    }

    public function deleteAction()
    {
        $this->_redirect('/admin/media/index');        
    }
    
}