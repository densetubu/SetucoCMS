<?php

/**
 * デザイン情報(閲覧画面)管理サービス
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
 * @package    Admin
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * デザイン情報管理クラス
 *
 * @package    Admin
 * @subpackage Model
 * @author     suzuki-mar
 * @todo デザイン情報を取得するDAOクラスを作成した方がいいのかな
 */
class Admin_Model_Design extends Common_Model_DesignAbstract
{

    /**
     * デザイン情報を更新する
     *
     * @param array 更新するデータ
     * @return int 何件更新したのか
     * @throws update文に失敗したら例外を発生させる
     * @author suzuki-mar
     */
    public function updateDesign($updateData)
    {

        //データは1件しかないないので、whereはいらない
        return $this->_designDao->update($updateData);
    }

    /**
     * 全てのデザイン名を取得する
     *
     * DBではなくて、デザイン情報ファイルからそれぞれデータを取得する
     *
     * @return array デザイン名リスト
     * @author suzuki-mar
     */
    public function findAllDesignNames()
    {
        foreach ($this->findAllDesignInfos() as $info) {
            $names[] = $info['name'];
        }

        return $names;
    }


    /**
     * 全てのデザイン情報ファイルを取得する
     *
     * DBではなくて、デザイン情報ファイルからそれぞれデータを取得する
     *
     * @return array デザイン情報リスト
     * @author suzuki_mar
     */
    public function findAllDesignInfos()
    {

        $designInfos = array();
        
        foreach($this->_getFrontViewAllPath() as $path) {
            $xml = new Zend_Config_Xml($path);
            $designInfos[] = $xml->toArray();
        }

        return $designInfos;
    }

    /**
     * 閲覧モジュールのビューのサブディレクトリのパスを全て取得する
     *
     * @return array サブディレクトリのパスの配列
     * @author suzuki-mar
     */
    private function _getFrontViewAllPath()
    {
        $dirPattern = $this->_getFrontViewBasePath() . "*";

        $infoFilePaths = array();

        foreach(glob($dirPattern) as $dirName) {

            $filePattern = $dirName . "/*";

            foreach (glob($filePattern) as $filePath) {
                if (basename($filePath) === 'info.xml') {
                    $infoFilePaths[] = $filePath;
                }
            }
        }

        return $infoFilePaths;
    }


    /**
     * 閲覧モジュールのビューのベースパスを取得する
     *
     * @return string 閲覧モジュールのビューのベースパス
     * @author suzuki_mar
     */
    private function _getFrontViewBasePath()
    {
        
        $filePaths = explode('/', __DIR__);

        $basePath = '/';

        for($i = 1; $i < (count($filePaths) - 2); $i++) {
            $basePath .= "{$filePaths[$i]}/";
        }

        $basePath .= 'default/views/';

        return $basePath;
    }

}
