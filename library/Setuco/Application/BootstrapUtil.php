<?php

class Setuco_Application_BootstrapUtil
{
    /**
     * インスタンス化不可
     * 
     * @return void
     */
    private function __construct() {}
    
    /**
     * ブートストラッパーからリソースを取り出す<br>
     * リソースが含まれていない場合は該当リソースのブートストラップを行う
     * 
     * @param Zend_Application_Bootstrap_Bootstrapper $bootstrapper
     * @param string $resourceName
     * @return Zend_Application_Bootstrap_BootstrapAbstract
     */
    public static function extractResource($bootstrapper, $resourceName)
    {
        if (!$bootstrapper->hasResource($resourceName)) {
            $bootstrapper->bootstrap($resourceName);
        }
        
        return $bootstrapper->getResource($resourceName);
    }
}
