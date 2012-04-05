<?php
/**
 * APIモジュールのファイル管理のコントローラ
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
 * @package    Api
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * ファイル管理画面の操作を行うコントローラ
 *
 * @package    Api
 * @subpackage Controller
 * @author     suzuki-mar
 */
class Api_MediaController extends Setuco_Controller_Action_Abstract
{

    /**
     * Mediaサービスクラスのオブジェクト
     * @var Api_Model_Media
     */
    private $_media = null;

    /**
     * 初期化処理
     *
     * @return void
     * @author akitsukada
     */
    public function init()
    {
        parent::init();

        $this->_media = new Api_Model_Media();

        $this->_helper->addHelper(new Setuco_Controller_Action_Helper_SetucoContextSwitch());
        $contextSwitch = $this->_helper->getHelper('setucoContextSwitch');
        
        $contextSwitch->addActionContext('index', 'json')
                ->initContext('json');

        
    }


    /**
     * メディア情報のリストをJSON形式で表示する
     *
     * @return void
     * @author suzuki-mar
     */
    public function indexAction()
    {
        $this->view->infos = $this->_media->findAllMedias();
    }
   
}
