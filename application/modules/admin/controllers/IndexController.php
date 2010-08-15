<?php

class Admin_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
    }
    
    public function updateAmbitionAction()
    {
    	$this->_redirect('/admin/index/index');
    }

    public function formGoalAction()
    {
    }

    public function updateGoalAction()
    {
    	$this->_redirect('/admin/index/form-goal');
    }

}

