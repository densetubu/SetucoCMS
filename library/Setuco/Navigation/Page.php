<?php
/**
 * ナビゲーションページの拡張クラスです。
 * 
 * Zend_Navigation_Page_Mvcのプロパティに加えてイメージファイルのパスを保持します。
 *
 * Copyright (c) 2010-2011 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * All Rights Reserved.
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
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
