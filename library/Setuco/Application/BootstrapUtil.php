<?php
/**
 * ブートストラッパーからリソースを取り出す
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Controller
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     Yuu Yamanaka
 */

/**
 * @category   Setuco
 * @package    Controller
 * @author     Yuu Yamanaka
 */


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
     * @author     Yuu Yamanaka
     */
    public static function extractResource($bootstrapper, $resourceName)
    {
        if (!$bootstrapper->hasResource($resourceName)) {
            $bootstrapper->bootstrap($resourceName);
        }
        
        return $bootstrapper->getResource($resourceName);
    }
}
