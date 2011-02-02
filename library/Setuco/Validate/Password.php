<?php

/**
 * パスワードで使用できる文字列だけかを調べる
 *
 * LICENSE: ライセンスに関する情報
 *
 * @category   Setuco
 * @package    Setuco_Validate
 * @copyright  Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @version
 * @link
 * @since      File available since Release 0.1.0
 * @author     suzuki_mar
 */

/**
 * @category    Setuco
 * @package     Setuco_Validate
 * @copyright   Copyright (c) 2010 SetucoCMS Project.
 * @license
 * @author      suzuki-mar
 */
class Setuco_Validate_Password extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'パスワードに使用できる文字は半角英数字とアンダースコア、バックスラッシュを除く半角記号です。',
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
        $allowSymbol = '!"#$%&\'()=~|\-^@\[;:\],.\/`{+*}>?';

        if (!preg_match("/^[a-zA-Z0-9{$allowSymbol}]+$/", $value)) {
            $this->_error(self::NOT_MATCH);
            return false;
        }

        return true;
    }

}
