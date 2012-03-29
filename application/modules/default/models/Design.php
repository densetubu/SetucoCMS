<?php

/**
 * 閲覧側の表示デザイン情報管理用サービス
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
 * @package    Default
 * @subpackage Model
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @link
 * @version
 * @since      File available since Release 0.1.0
 * @author     suzuki-mar
 */

/**
 * 表示デザイン情報管理クラス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar
 */
class Default_Model_Design extends Common_Model_DesignAbstract
{


    /**
     * カテゴリー情報を取得する
     *
     * @return array カテゴリー情報 取得できなかったらfalse
     * @author suzuki-mar
     */
    public function findCategoryList()
    {
        //未分類以外のカテゴリーを取得する
        $categories = $this->_categoryDao->loadAllCategories();

        if (empty($categories)) {
            return false;
        }


        //使用されているカテゴリーを取得する
        $usedCategories = $this->_categoryDao->loadUsedCategories();


        if (!empty($usedCategories)) {
            //使用されているカテゴリーのIDの配列を取得する
            foreach ($usedCategories as $value) {
                $useIds[] = $value['id'];
            }

            foreach ($categories as &$value) {
                $value['is_used'] = in_array($value['id'], $useIds);
            }
            unset($value);

            //ひとつも使用されていない場合は、すべて失敗にする
        } else {
            foreach ($categories as &$value) {
                $value['is_used'] = false;
            }
            unset($value);
        }

        return $categories;
    }

    /**
     * カテゴリーIDを指定してカテゴリー情報を取得する
     * （DbTableクラスのfindメソッドへの委譲）
     *
     * @param int $id 取得したいページのカテゴリーID
     * @return array|boolean 該当のデータが存在すれば配列データ、存在しなければfalseを返す
     * @author akitsukada
     */
    public function findCategory($id)
    {
        $result = $this->_categoryDao->loadByPrimary($id);

        if (is_null($result)) {
            return false;
        }
        return $result;
    }

}

