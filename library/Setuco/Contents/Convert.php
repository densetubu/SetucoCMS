<?php
/**
 * 記事を閲覧用に変換するクラスのFacadeクラスです
 * Convertのエンティティクラスを使用して記事を変換します
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
 * @subpackage Contents
 * @copyright  Copyright (c) 2010 SetucoCMS Project.(http://sourceforge.jp/projects/setucocms)
 * @license    http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @version
 * @link
 * @since       File available since Release 0.1.0
 * @author      suzuki_mar
 */

/**
 * 
 * @package    Setuco
 * @subpackage Contents
 * @author     suzuki_mar
 */
class Setuco_Contents_Convert
{
    /**
     * 記事を変換するエレメント配列
     */
    private $_elements;


    /**
     * エレメントを追加する
     *
     * @param Setuco_Contents_ConvertInterface $element 変換に使用するエレメント
     */
    public function addElement(Setuco_Contents_ConvertInterface $element)
    {
        $this->_elements[] = $element;
    }

    /**
     * 記事を閲覧用に変換する
     * 変換する種類のエレメントを追加しておく必要があります
     *
     * @param array $enetry 変換する記事データ
     * @return array 変換した記事データ
     * @author suzuki-mar
     */
    public function convert(array $entry)
    {
        if (empty($this->_elements)) {
            return $entry;
        }

        foreach ($this->_elements as $element) {
            $entry = $element->convert($entry);
        }

        return $entry;
    }


}
