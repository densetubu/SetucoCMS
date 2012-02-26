<?php

/**
 * 確認用と同じ値かをチェックする
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
 * @package     Setuco
 * @subpackage  Validate
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @package     Setuco
 * @subpackage  Validate
 * @author      suzuki-mar
 */
class Setuco_Validate_Match extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => '確認用と一致しません。',
    );

    /**
     * 配列のチェックするkeyの名前
     * このkyeをと同じかをチェックする
     *
     * @var string
     */
    private $_checkKey = null;

    /**
     *
     * @author suzuki-mar
     */
    public function  __construct($params = null)
    {
        if (isset($params['check_key'])) {
            $this->_checkKey = $params['check_key'];
        }
    }

    /**
     * 確認用と同じかをチェックする
     *
     * @param string $value チェックする値
     * @param string $confirm $valueと同じかをチェックするもの
     * @return boolean 確認用と同じか
     * @author suzuki-mar
     */
    public function isValid($value, $confirm = null)
    {
        //第2引数がなかったら例外を発生させる インターフェースのため定義はデフォルト引数
        if (is_null($confirm)) {
            throw new Setuco_Exception('第２引数($confimr)の値がありません。');
        }

        if (is_array($confirm) && !isset($confirm[$this->_checkKey])) {
            throw new Setuco_Exception('check_keyで指定した配列の要素がありません。');
        }

        if (is_array($confirm)) {
            if ($value !== $confirm[$this->_checkKey]) {
                $this->_error(self::NOT_MATCH);
                return false;
            }
        } else {
            if ($value !== $confirm) {
                $this->_error(self::NOT_MATCH);
                return false;
            }
        }

        return true;
    }

}
