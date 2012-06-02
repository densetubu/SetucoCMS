<?php
/**
 * インストーラのエラーコントローラー
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
 * @package    Install
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 1.2.0
 * @author     Takayuki Otake
 */

/**
 * エラーコントローラー
 *
 * @package    Install
 * @subpackage Controller
 * @author     Takayuki Otake
 */
class Install_ErrorController extends Setuco_Controller_Action_ErrorAbstract
{
    /**
     * エラー画面を表示する
     *
     * @return void
     * @author Takayuki Otake
     */
    public function errorAction()
    {

        // URL(admin/error/error)で直接アクセスの対策
        if (is_null($this->_getParam('error_handler'))) {
            throw new Setuco_Controller_IllegalAccessException('ページがありません。', 404);
        }

        if (APPLICATION_ENV === 'production') {
            $this->_productionOperation();
        } else if (APPLICATION_ENV === 'development') {
            $this->_developmentOperation();
        } else {
            $this->_developmentOperation();
        }
    }

}
