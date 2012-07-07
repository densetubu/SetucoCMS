<?php

/**
 * APIモジュールのテンプレート管理用サービス
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
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * APIモジュールのテンプレート管理サービス
 *
 * @package    Api
 * @subpackage Model
 * @author     suzuki-mar
 */
class Api_Model_Template extends Common_Model_TemplateAbstract
{

    /**
     * 全てのテンプレートデータの情報を取得する
     *
     * ファイルパスはフルパス(URL)
     *
     * @author suzuki-mar
     * @return array 全てのテンプレートデータ情報
     * @todo httpsに対応させる
     */
    public function findAllTemplateInfos()
    {
        $templates = $this->findAllTemplates();
        
        //必要なデータだけしか出力しない
        $infos = array();
        foreach ($templates as $template) {
            $info['title']          = $template['title'];
            $info['explanation']    = $template['explanation'];

            $fileName       = "{$template['file_name']}.html";
            $info['url']    = $this->_fixUrlPath($fileName);

            $infos[] = $info;
        }
        
        return $infos;
    }

    /**
     * テンプレートのパスをフルパス(URLにする
     *
     * @param string $filePath テンプレートのファイル名
     * @author suzuki-mar
     */
    private function _fixUrlPath($fileName)
    {
        $templateDir = preg_replace("/.+public\/(.+)/", "$1", $this->_getBasePath());
        return "http://{$_SERVER['HTTP_HOST']}/{$templateDir}{$fileName}";
    }

}
