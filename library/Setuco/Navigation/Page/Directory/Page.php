<?php
/**
 * ナビゲーションページの拡張クラスです。
 * 
 * Zend_Navigation_Page_Mvcのプロパティに加えてHrefを２種類取得する機能を持ちます。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Navigation
 * @subpackage Page_Directory
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     charlelsvineyard
 */

/**
 * @category   Setuco
 * @package    Navigation
 * @subpackage Page_Directory
 * @author     charlelsvineyard
 */
class Setuco_Navigation_Page_Directory_Page extends Zend_Navigation_Page_Mvc
{
    /**
     * 編集用のURLを取得します。
     * 
     * @return string 編集用のURL
     * @author charlesvineyard
     */
    public function getEditHref()
    {
        $module = $this->getModule();
        $this->setModule('admin');       
        $action = $this->getAction();
        $this->setAction('form');
        $href = $this->getHref();
        $this->setModule($module);
        $this->setAction($action);
        return $href;
    }

    /**
     * 閲覧用のURLを取得します。
     * 
     * @return string 閲覧用のURL
     * @author charlesvineyard
     */
    public function getViewHref()
    {
        $module = $this->getModule();
        $this->setModule('default');       
        $href = $this->getHref();
        $this->setModule($module);
        return $href;
    }
    
}
