<?php
/**
 * 閲覧側のタグ管理用サービス
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
 * タグ管理サービス
 *
 * @package    Default
 * @subpackage Model
 * @author     suzuki-mar
 */
class Default_Model_Tag extends Common_Model_TagAbstract
{
    /**
     * タグの絶対値
     * Zend_Tagの関係で定義している
     *
     * @var array
     */
    protected $_tagSpread = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);

    /**
     * クラス設定の初期設定をする
     *
     * @return void
     * @author suzuki-mar
     */
    public function __construct()
    {
        $this->_tagDao = new Common_Model_DbTable_Tag();
    }

    /**
     * タグクラウドを取得する
     *
     * @return array タグクラウドのデータ　title id value 値を取得できなかった場合はfalse
     * @author suzuki-mar
     */
    public function getTagClouds()
    {
        $tags  = $this->_tagDao->loadTagCloudInfos();


        //からならfalseを返す
        if (empty($tags)) {
            return false;
        }


        //タグのカウントの配列を作成する タグが多い順
        //タグは、1番からカウントする
        $i = 1;
        $counts = array();
        foreach ($tags as $value) {
            //同じものは配列に入れない
            if (!in_array($value['count'], $counts)) {
                $counts[$i] = $value['count'];
                $i++;
            }

            //タグが、最大のレベルまでいったらループを終了する
            if ($i > 10) {
                break;
            }
        }

        //最小のカウントを取得する
        $minCount = min($counts);

        //タグのレベルを設定する
        foreach ($tags as &$value) {
            //$countsの最小よりも小さい場合は、最小と同じレベルにする
            if ($value['count'] < $minCount) {
                $value['level'] = 10;
            } else {
                $searchKeys = array_search($value['count'], $counts);
                $value['level'] = $searchKeys;
            }
        }
        unset($value);


        //更新順でソートする
        foreach ($tags as $value) {
            $dates[] = $value['update_date'];
        }
        array_multisort($dates, SORT_DESC, $tags);

        $result = $tags;

        return $result;

    }



}
