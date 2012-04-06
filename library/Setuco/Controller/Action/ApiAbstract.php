<?php

/**
 * apiモジュールの共通のコントローラーです
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
 * @package    Setuco
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @package    Setuco
 * @subpackage Controller_Action
 * @author      suzuki-mar
 */
abstract class Setuco_Controller_Action_ApiAbstract extends Setuco_Controller_Action_Abstract
{

    /**
     * モジュールの共通の前処理をする
     *
     * @author suzuki-mar
     */
    public function  preDispatch()
    {
        parent::preDispatch();

        $this->_helper->addHelper(new Setuco_Controller_Action_Helper_SetucoContextSwitch());
        $contextSwitch = $this->_helper->getHelper('setucoContextSwitch');

        //指定がない限り全アクションでJSONを返す
        $contextSwitch->addActionContext($this->getRequest()->getParam('action'), 'json')
                ->initContext('json');
    }
}
