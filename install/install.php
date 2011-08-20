<?php

include_once('classes/View.class.php');
include_once('classes/Common.class.php');
include_once('classes/Db.class.php');

$common = new Common();
$view   = new View();


$params = $common->getParams();
if ( !empty($_POST['confirm']) || !empty($_POST['action']) ){

    if ( ! $common->isError() ){
    
        if ( !empty($_POST['action']) ){
            $view->setMainArea('form_action');
            
            $dbh = new Db('localhost', 'setucocms', 'setucocms', 'setucocms');
            
            $querys = $common->getInitializeTablesSql();
            foreach ( $querys as $query ){
              $dbh->query($query);
            }
            
            $result = $dbh->updateSite($params);
            
        }
        elseif ( !empty($_POST['confirm'] )) {
            $view->setMainArea('form_confirm');
        }
      
    }
    else {

        $params = $common->getParams();  
        
        if ( $common->isParam($params) === false ){
          $params = $common->getDefaultParam();
        }
        
        $common->clearError();
        
    }
    $view->error = $common->error;
}
else {
// 
    $view->error = $common->clearError();

}


$view->setParams($params);
$view->error = $common->error;


$view->action();
