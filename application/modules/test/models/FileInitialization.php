<?php
/**
 * テストに使用するFileを初期化する
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
 * @package    Test
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * @category   Setuco
 * @package    Test
 * @subpackage Model
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author     suzuki-mar
 */

class Test_Model_FileInitialization
{

    private $_dirPaths = array();

    /**
     * コピーするファイル名のパスリストを取得する
     *
     * @return array  コピーするファイル名のパスリスト
     * @author suzuki-mar
     */
    public function getFilePathList()
    {
        $this->_findDirPaths(Setuco_Data_Constant_DirPath::FIXTURE_FILE_MEDIA_PATH());

        $this->_dirPaths[] = Setuco_Data_Constant_DirPath::FIXTURE_FILE_TEMPLATE_PATH();

        $filePaths = array();
        foreach ($this->_dirPaths as $dirPath) {
            $globPattern = "{$dirPath}/*";

            foreach (glob($globPattern) as $filePath) {
                $replaceDirPath = Setuco_Data_Constant_DirPath::FIXTURE_FILE_PATH() . DIRECTORY_SEPARATOR;
                $filePaths[] = str_replace($replaceDirPath, '', $filePath);
            }
        }

        return array_unique($filePaths);
    }

    /**
     * fixtureのファイルをコピーする
     *
     * すでにファイルが存在する場合はコピーしない
     *
     * @author suzuki-mar
     */
    public function copyFixtureFile()
    {
        foreach ($this->getFilePathList() as $filePath) {
            $source = Setuco_Data_Constant_DirPath::FIXTURE_FILE_PATH() . DIRECTORY_SEPARATOR . $filePath;
            $dest = Setuco_Data_Constant_DirPath::PUBLIC_PATH() . DIRECTORY_SEPARATOR . $filePath;

            $destDirPath = pathinfo($dest, PATHINFO_DIRNAME);

            if (!file_exists($destDirPath)) {
                //アップロードするディレクトリを全て作成する
                mkdir($destDirPath, 0775, true);
            }

            //ファイルが存在する場合は上書きしない
            if (!file_exists($dest)) {
                copy($source, $dest);
            }
        }
    }

    /**
     * アップロードしているファイルを削除する
     *
     * フィクスチャーに存在するファイル名しか削除しない
     *
     * @author suzuki-mar
     */
    public function deleteUploadFile()
    {
        foreach($this->getFilePathList() as $fileName) {
            $filePath = Setuco_Data_Constant_DirPath::PUBLIC_PATH() . DIRECTORY_SEPARATOR . $fileName;

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

    }



    private function _findDirPaths($baseDirName)
    {
        $globPattern = $baseDirName . DIRECTORY_SEPARATOR . '*';

        foreach (glob($globPattern, GLOB_ONLYDIR) as $dirName) {
            $this->_dirPaths[] = $dirName;

            $this->_findDirPaths($dirName);
        }
    }

}
