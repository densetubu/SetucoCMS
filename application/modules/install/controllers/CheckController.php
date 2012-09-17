<?php

/**
 * 設置する環境が整っているか確認をするコントローラ
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
 * @subpackage Controller
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @since      File available since Release 1.6.0
 * @author     Takayuki Otake
 */

/**
 * SetucoCMSを設置する環境が整っているか確認をするコントローラ
 *
 * @package    Install
 * @subpackage Controller
 * @author     Takayuki Otake
 */
class Install_CheckController extends Setuco_Controller_Action_Abstract
{
    /**
     * インストール環境が揃っているか確認するアクション
     *
     * @author Takayuki Otake
     */
    public function indexAction()
    {
        $allOk = true;
        $requiredOk = true;

        $dirErrors = array();
        if (!Setuco_Util_Media::isWritableUploadDir()) {
            $dirErrors[] = Setuco_Data_Constant_Media::MEDIA_UPLOAD_DIR_FULLPATH() . '　が存在しないか、書き込みできません。';
        }
        if (!Setuco_Util_Media::isWritableThumbDir()) {
            $dirErrors['upload_thumb'] = Setuco_Data_Constant_Media::MEDIA_THUMB_DIR_FULLPATH() . '　が存在しないか、書き込みできません。';
        }
        if (!Setuco_Util_Config::isWritableConfigDir()) {
            $dirErrors['configs'] = Setuco_Data_Constant_Config::CONFIG_DIR_FULLPATH() . '　が存在しないか、書き込みできません。';
        }
        if (count($dirErrors) > 0) {
            $this->view->dirErrors = $dirErrors;
        }

        $requiredPhpExtensions = Setuco_Util_Environment::checkRequiredPhpExtensions();
        foreach ($requiredPhpExtensions as $ext => $loaded) {
            if (!$loaded) {
                $allOk = false;
                $requiredOk = false;
            }
        }
        $this->view->phpExtensions = $requiredPhpExtensions;


        $requiredApacheModules = Setuco_Util_Environment::checkRequiredApacheModules();
        foreach ($requiredApacheModules as $module => $loaded) {
            if (!$loaded) {
                $all_ok = false;
                $required_ok = false;
            }
        }
        $this->view->apacheModules = $requiredApacheModules;


        $this->view->allOk = $allOk;
        $this->view->requiredOk = $requiredOk;
    }

}
