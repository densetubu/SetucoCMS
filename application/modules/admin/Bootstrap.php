<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initLayout()
    {
        $options = array('layout'     => 'layout',
                         'layoutPath' => APPLICATION_PATH . '/modules/admin/views/layouts',
                         'content'    => 'content');
        Zend_Layout::startMvc($options);
    }
}