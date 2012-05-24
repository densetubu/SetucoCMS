<?php

/**
 * APIのファイル管理用サービス
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
 * ファイル管理クラス
 *
 * @package    Api
 * @subpackage Model
 * @author     suzuki-mar
 */
class Api_Model_Media extends Common_Model_MediaAbstract
{

    /**
     * 全てのメディアデータの情報を取得する
     *
     * ファイルパスはフルパス(URL)
     *
     * @return array 全てのメディアデータ情報
     * @author suzuki-mar
     * @todo httpsに対応させる
     */
    public function findAllMediaInfos()
    {
        $medias = $this->findAllMedias();
        return $this->_fixAllUrlPath($medias);
    }

    /**
     * 画像のメディアデータの情報を取得する
     *
     * ファイルパスはフルパス(URL)
     *
     * @return array 画像のメディアデータ
     * @author suzuki-mar
     */
    public function findImageMediaInfos()
    {
        $medias = $this->findImageMedias();
        return $this->_fixAllUrlPath($medias);
    }

    /**
     * 画像以外のメディアデータを取得する
     *
     * ファイルパスはフルパス(URL)に変更している
     *
     * @return array 画像以外のメディアデータのリスト
     * @author suzuki-mar
     */
    public function findEtcMediaInfos()
    {
        $medias = $this->_addModelDatas($this->_mediaDao->loadEtcMedias());
        return $this->_fixAllUrlPath($medias);
    }

    /**
     * データのURLをフルパス(URL)にする
     *
     * @param array $targetDatas URLを修正するデータ
     * @return array データをURLに修正した物
     * @author suzuki-mar
     */
    private function  _fixAllUrlPath(array $targetDatas)
    {
        foreach ($targetDatas as &$media) {
            $media['uploadUrl'] = $this->_fixUrlPath($media['uploadUrl']);
            $media['thumbUrl'] = $this->_fixUrlPath($media['thumbUrl']);
        }
        unset($media);

        return $targetDatas;
    }

    /**
     * 画像のパスをフルパス(URL）にする
     *
     * @param string $filePath 画像のファイルパス
     * @author suzuki-mar
     */
    private function _fixUrlPath($filePath)
    {
        return "http://{$_SERVER['HTTP_HOST']}{$filePath}";
    }

}
