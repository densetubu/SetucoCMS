<?php

/**
 * 確認用と同じ値かをチェックする
 *
 * LICENSE: ライセンスに関する情報
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
