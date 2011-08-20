<?php
/*
 *Error Code
 *  0: true
 *  1: No input
 *  2: Out of length
 *  3: Can't use character.
 *  5: $password !== $password2
 *
*/

class View {
    
    var $params　= array();
    var $error  = array();
    var $mainArea;// = 'form';
    
    public function __construct()
    {
        $this->mainArea = 'form';
    }
    public function set($name, $value)
    {
        $this->$name = $value;
    }
    
    public function setMainArea($mainArea)
    {
        $this->mainArea = $mainArea;
    }
    
    public function setParams($params)
    {
        $this->params = $params;
    }
    
    public function getErrorMessage($param)
    {
        switch ($this->error[$param]){
            case 1:
                return '入力が空です。';
                break;
            case 3:
                return '使用できない文字が含まれています。';
                break;
            case 5:
                return '入力された2つのパスワードに違いがあります。';
                break;
            default:
                return '';
                break;
        }
    }
    
    public function getMainArea()
    {
        return $this->mainArea;
    }
    
    public function action()
    {
        include_once('views/_header.phtml');
        include_once('views/'. $this->getMainArea() . '.phtml');
        include_once('views/_footer.phtml');
    }
    
    
}
