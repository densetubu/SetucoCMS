<?php

/**
 * パスワードで使用できる文字列だけかを調べる
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
class Setuco_Validate_Password extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'パスワードに使用できる文字は半角英数字[0-9][a-z][A-Z]と一部の半角記号[! " # - $ % & \' ( ) = ~ | ^ @ [ ; : ] , . / ` { + * } < > ?]のみです。',
    );

    /**
     * パスワードに使用できる文字化をチェックする
     *
     * @param string $value チェックする値
     * @param string $confirm $valueと同じかをチェックするもの
     * @return boolean 確認用と同じか
     * @author suzuki-mar
     */
    public function isValid($value)
    {
        $allowSymbol = '!"#$%&\'()=~|\-^@\[;:\],.\/`{+*}<>?';

        if (!preg_match("/^[a-zA-Z0-9{$allowSymbol}]+$/", $value)) {
            $this->_error(self::NOT_MATCH);
            return false;
        }

        return true;
    }

}
