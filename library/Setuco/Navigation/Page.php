<?php
/**
 * ナビゲーションページの拡張クラスです。
 * 
 * Zend_Navigation_Page_Mvcのプロパティに加えてイメージファイルのパスを保持します。
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package     Setuco
 * @subpackage  Navigation
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     charlelsvineyard
 */

/**
 * @package     Setuco
 * @subpackage  Navigation
 * @author  charlelsvineyard
 */
class Setuco_Navigation_Page extends Zend_Navigation_Page_Mvc
{
    /**
     * イメージファイルのパス
     * 
     * @var string
     */
    private $_src;

    /**
     * イメージファイルのパスを設定します。
     * 
     * @param string $src イメージファイルのパス
     * @return null
     * @author charlesvineyard 
     */
    public function setSrc($src)
    {
        $this->_src = $src;
    }

    /**
     * イメージファイルのパスを取得します。
     * 
     * @return string イメージファイルのパス
     * @author charlesvineyard
     */
    public function getSrc()
    {
        return $this->_src;
    }
}
